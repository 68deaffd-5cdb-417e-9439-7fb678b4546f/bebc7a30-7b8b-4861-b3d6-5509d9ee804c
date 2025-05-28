<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Spatie\Permission\Models\Role;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\Clock\SystemClock;

class KeycloakAuth
{
    protected Configuration $jwtConfig;

    public function __construct()
    {
        $publicKeyPath = storage_path('oauth-public.pem');

        $this->jwtConfig = Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::plainText('dummy'),
            InMemory::file($publicKeyPath)
        );
    }

    public function handle(Request $request, Closure $next)
    {
        $bearerToken = $request->bearerToken();

        if (!$bearerToken) {
            return response()->json(['error' => 'Missing token'], 401);
        }

        try {
            $token = $this->jwtConfig->parser()->parse($bearerToken);
            assert($token instanceof UnencryptedToken);

            // Verify signature
            if (! $this->jwtConfig->validator()->validate(
                $token,
                new \Lcobucci\JWT\Validation\Constraint\SignedWith(
                    $this->jwtConfig->signer(),
                    $this->jwtConfig->verificationKey()
                ),
                new \Lcobucci\JWT\Validation\Constraint\LooseValidAt(SystemClock::fromUTC())
            )) {
                return response()->json(['error' => 'Invalid or expired token'], 401);
            }

            $user = User::where('keycloak_id', $token->claims()->get('sub'))->first();
            if($user == null) {
                return response()->json(['error' => 'User not found'], 401);
            }

            $roles = $token->claims()->get('resource_access')['demo_client']['roles'];

            $request->attributes->set('user', $user);
            $request->attributes->set('roles', $roles);

            $permissions = [];
            foreach (Role::all() as $role) {
                /** @var $role Role */
                if(in_array($role->name, $roles)) {
                    foreach ($role->getAllPermissions() as $permission) {
                        $permissions[$permission->name] = true;
                    }
                }

            };
            $request->attributes->set('permissions', array_keys($permissions));
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Invalid token', 'message' => $e->getMessage()], 401);
        }



        return $next($request);
    }
}

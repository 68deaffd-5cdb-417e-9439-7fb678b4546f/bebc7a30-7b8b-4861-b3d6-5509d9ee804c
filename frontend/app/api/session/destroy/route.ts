import { cookies } from 'next/headers';
import { getIronSession } from 'iron-session';
import { redirect } from 'next/navigation';


export async function GET(request: Request) {
    const session = await getIronSession(await cookies(), { password: process.env.IRON_SESSION_SECRET!, cookieName: process.env.IRON_SESSION_OAUTH_COOKIE! });
    session.destroy();
    return redirect('/logout')
}
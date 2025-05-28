import { NextRequest, NextResponse } from 'next/server';
import { getIronSession } from 'iron-session';

export async function middleware(req: NextRequest) {
    const res = NextResponse.next();

    const pathname = req.nextUrl.pathname;

    if (pathname === '/logout') {
        return res;
    }

    const session = await getIronSession(req, res, { password: process.env.IRON_SESSION_SECRET!, cookieName: process.env.IRON_SESSION_OAUTH_COOKIE! });

    const token = session.token as string | undefined;

    if (!token) {
        // Redirect to login if no session
        return NextResponse.redirect(new URL(process.env.PUBLIC_BACKEND_LOGIN_PAGE!, req.url));
    }

    try {
        const payload = JSON.parse(atob(token.split('.')[1])); // Decode JWT payload
        const now = Math.floor(Date.now() / 1000);

        if (payload.exp && now > payload.exp) {
            // Token expired: call refresh
            // const refreshRes = await fetch(new URL('/api/refresh', req.url).toString(), {
            //     method: 'POST',
            //     headers: {
            //         //   cookie: req.headers.get('cookie') || '',
            //     },
            // });

            // if (!refreshRes.ok) {
            //     return NextResponse.redirect(new URL(process.env.LOGIN_PAGE!, req.url));
            // }
            // TODO not handled
            return NextResponse.redirect(new URL(process.env.PUBLIC_BACKEND_LOGIN_PAGE!, req.url));
        }

        return res;
    } catch (err) {
        return NextResponse.redirect(new URL(process.env.PUBLIC_BACKEND_LOGIN_PAGE!, req.url));
    }
}

export const config = {
    matcher: ['/((?!api|_next|static|favicon.ico).*)'],
};
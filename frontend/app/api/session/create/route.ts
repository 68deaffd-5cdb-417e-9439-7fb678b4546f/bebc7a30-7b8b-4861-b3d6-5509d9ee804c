import { cookies } from 'next/headers';
import { getIronSession } from 'iron-session';
import { redirect } from 'next/navigation';


export async function POST(request: Request) {
    const formData = await request.formData()

    const session = await getIronSession(await cookies(), { password: process.env.IRON_SESSION_SECRET!, cookieName: process.env.IRON_SESSION_OAUTH_COOKIE! });
    session.token = formData.get('token');
    await session.save();

    return redirect('/')
}
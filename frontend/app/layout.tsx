import '@mantine/core/styles.css';

import React from 'react';
import { ColorSchemeScript, mantineHtmlProps, MantineProvider } from '@mantine/core';
import { theme } from '../theme';
import { getIronSession } from 'iron-session';
import { cookies } from 'next/headers';
import { SessionProvider } from '@/app/context/SessionContext';

export const metadata = {
  title: 'Mantine Next.js template',
  description: 'I am using Mantine with Next.js!',
};

export default async function RootLayout({ children }: { children: any }) {
  const session = await getIronSession(await cookies(), { password: process.env.IRON_SESSION_SECRET!, cookieName: process.env.IRON_SESSION_OAUTH_COOKIE! });

  return (
    <html lang="en" {...mantineHtmlProps}>
      <head>
        <ColorSchemeScript />
        <link rel="shortcut icon" href="/favicon.svg" />
        <meta
          name="viewport"
          content="minimum-scale=1, initial-scale=1, width=device-width, user-scalable=no"
        />
      </head>
      <body>
        <MantineProvider theme={theme}>
          <SessionProvider session={{
            loggedIn: Object.keys(session).length > 0,
            token: session.token,
          }}>
            {children}
          </SessionProvider>
        </MantineProvider>
      </body>
    </html>
  );
}

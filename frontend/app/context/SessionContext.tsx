'use client';

import { createContext, ReactNode, useContext } from 'react';

export interface SessionData {
  loggedIn: boolean;
  nickname: string;
}

const SessionContext = createContext<SessionData | null>(null);

export const useSession = () => {
  const context = useContext(SessionContext);
  if (!context) throw new Error('useSession must be used inside SessionProvider');
  return context;
};

export function SessionProvider({ children, session }: { children: React.ReactNode, session: SessionData | null }) {
  return (
    <SessionContext.Provider value={session}>
      {children}
    </SessionContext.Provider>
  );
}
'use client'

import {
    IconBook,
    IconChartPie3,
    IconChevronDown,
    IconCode,
    IconCoin,
    IconFingerprint,
    IconHeart,
    IconLogout,
    IconMessage,
    IconNotification,
    IconPlayerPause,
    IconSettings,
    IconStar,
    IconSwitchHorizontal,
    IconTrash,
} from '@tabler/icons-react';
import {
    Anchor,
    Avatar,
    Box,
    Burger,
    Button,
    Center,
    Collapse,
    Divider,
    Drawer,
    Group,
    HoverCard,
    Menu,
    ScrollArea,
    SimpleGrid,
    Text,
    ThemeIcon,
    UnstyledButton,
    useMantineTheme,
} from '@mantine/core';
import { useDisclosure } from '@mantine/hooks';
import { MantineLogo } from '@mantinex/mantine-logo';
import classes from './HeaderMegaMenu.module.css';
import { useState } from 'react';
import clsx from 'clsx';
import { redirect } from 'next/navigation';
import { getIronSession } from 'iron-session';
import { useSession } from '@/app/context/SessionContext';
import Link from 'next/link';

export function HeaderMegaMenu() {
    const logout = () => {
        redirect('/api/session/destroy')
    }

    return (
        <Box pb={120}>
            <header className={classes.header}>
                <Group justify="space-between" h="100%">
                    
                    <Group h="100%" gap={0} visibleFrom="sm">
                        <Link href={'/'} className={classes.link}>
                            My Campaigns
                        </Link>
                    </Group>

                    <Group visibleFrom="sm">
                        <Link href={'/campaign'}>
                            <Button>Create Campaign</Button>
                        </Link>

                        <Button variant={'default'} onClick={logout}>Logout</Button>

                    </Group>


                </Group>
            </header>
        </Box>
    );
}
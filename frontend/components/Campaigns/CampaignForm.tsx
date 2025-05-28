'use client'

import {useSession} from '@/app/context/SessionContext';
import { useState } from 'react';
import { TextInput, Textarea, NumberInput, Button, Group } from '@mantine/core';
import dayjs from 'dayjs';
import { useForm } from '@mantine/form';

export function CampaignForm() {
    const session = useSession();

    const [loading, setLoading] = useState(false);

    const form = useForm({
        initialValues: {
            title: 'Campaign title',
            description: 'Campaign description',
            goalAmount: 1000,
            startsAt: '2025-01-01 00:00:00',
            endsAt: '2030-01-01 00:00:00',
        },
    });

    const handleSubmit = async (values: typeof form.values) => {
        setLoading(true);

        try {
            const response = await fetch(`${process.env.PUBLIC_BACKEND_API}/campaigns`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/ld+json',
                    Authorization: `Bearer ${session.token}`,
                },
                body: JSON.stringify({
                    title: values.title,
                    description: values.description,
                    goalAmount: values.goalAmount,
                    startsAt: dayjs(values.startsAt).format('YYYY-MM-DD HH:mm:ss'),
                    endsAt: dayjs(values.endsAt).format('YYYY-MM-DD HH:mm:ss'),
                    status: 'draft',
                }),
            });

            if (!response.ok) throw new Error('Failed to create campaign');

            const data = await response.json();
            alert('Campaign created successfully!');

            window.location = '/'
        } catch (error) {
            console.error(error);
            alert('Something went wrong.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <>
            <form onSubmit={form.onSubmit(handleSubmit)}>
                <TextInput label="Title" placeholder="Campaign title" {...form.getInputProps('title')} required/>
                <Textarea label="Description" placeholder="Campaign description" {...form.getInputProps('description')}
                          required/>
                <NumberInput label="Goal Amount" {...form.getInputProps('goalAmount')} required/>
                <TextInput label="Starts At" {...form.getInputProps('startsAt')} required/>
                <TextInput label="Ends At" {...form.getInputProps('endsAt')} required/>

                <Group mt="md">
                    <Button type="submit" loading={loading}>Create Campaign</Button>
                </Group>
            </form>
        </>
    );
}

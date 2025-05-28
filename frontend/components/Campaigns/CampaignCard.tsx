import { ActionIcon, Avatar, Badge, Button, Card, Group, Progress, Text } from '@mantine/core';
import Link from 'next/link';

export function CampaignCard({ item }: { item: any }) {
    return (
        <Card withBorder padding="lg" radius="md">
            

            <Text fz="lg" fw={500} mt="md">
                {item.title}
            </Text>
            <Text fz="sm" c="dimmed" mt={5}>
                {item.description}
            </Text>

            <Text c="dimmed" fz="sm" mt="md">
                Goal:{' '}
                <Text span fw={500} c="bright">
                    ${item.goalAmount}
                </Text>
            </Text>

            <Text c="dimmed" fz="sm" mt="md">
                Ends:{' '}
                <Text span fw={500} c="bright">
                    {item.endsAt}
                </Text>
            </Text>

            
            <Group mt="xs">
                <Link href={`/campaign/${item.id}`}>
                    <Button radius="md" style={{ flex: 1 }}>
                        Show details
                    </Button>
                </Link>
            </Group>
        </Card>
    );
}
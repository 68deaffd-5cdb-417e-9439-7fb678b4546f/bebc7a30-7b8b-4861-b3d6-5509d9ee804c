'use client'

import { useSession } from '@/app/context/SessionContext';
import { Anchor, Card, Divider, Flex, Grid, List, Loader, Stack, Text, Title } from '@mantine/core';
import { useEffect, useState } from 'react';
import { CampaignCard } from './CampaignCard';
import { CampaignDonateForm } from './CampaignDonateForm';

export function CampaignView({id}) {
  const session = useSession();
  const [campaign, setCampaign] = useState(null);
  const [donations, setDonations] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const fetchCampaignData = async () => {
    try {
      setLoading(true);
      const [campaignRes, donationsRes] = await Promise.all([
        fetch(`${process.env.PUBLIC_BACKEND_API}/campaigns/${id}`, {
          headers: {
            Authorization: `Bearer ${session.token}`,
          },
        }),
        fetch(`${process.env.PUBLIC_BACKEND_API}/campaigns/${id}/donations`, {
          headers: {
            Authorization: `Bearer ${session.token}`,
          },
        }),
      ]);

      if (!campaignRes.ok || !donationsRes.ok) {
        throw new Error('Failed to fetch campaign or donations');
      }

      const campaignData = await campaignRes.json();
      const donationsData = await donationsRes.json();

      setCampaign(campaignData);
      setDonations(donationsData);
    } catch (err: any) {
      setError(err.message || 'Something went wrong');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchCampaignData();
  }, [id]);

  if (loading) return <Flex justify={'center'}><Loader size={30} /></Flex>;
  if (error) return <p>Error: {error}</p>;
  if (!campaign) return <p>No campaign found.</p>;

  return (
    <Stack spacing="md">
      <Card shadow="sm" padding="lg" radius="md" withBorder>
        <Title order={2}>{campaign.title}</Title>
        <Text mt="sm" size="sm" color="dimmed">
          {campaign.description}
        </Text>
        <CampaignDonateForm id={id} onSuccess={fetchCampaignData} />
      </Card>

      <Divider label="Donations" labelPosition="center" />

      {donations.member.length === 0 ? (
        <Text>No donations yet.</Text>
      ) : (
        <Card shadow="sm" padding="lg" radius="md" withBorder>
          <List spacing="sm" size="sm" center>
            {donations.member.map((donation) => (
              <List.Item key={donation.id}>
                <i>{donation.donatedAt}</i> donation <b>${donation.amount}</b>
              </List.Item>
            ))}
          </List>
        </Card>
      )}
    </Stack>
  );
}

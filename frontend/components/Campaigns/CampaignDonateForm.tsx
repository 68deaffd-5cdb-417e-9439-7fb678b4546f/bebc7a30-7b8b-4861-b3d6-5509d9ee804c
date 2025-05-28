'use client'

import { useSession } from '@/app/context/SessionContext';
import { Alert, Anchor, Button, Card, Divider, Flex, Grid, Group, List, Loader, Stack, Text, TextInput, Title } from '@mantine/core';
import { useEffect, useState } from 'react';
import { CampaignCard } from './CampaignCard';
import { useRouter } from 'next/navigation';

export function CampaignDonateForm({ id, onSuccess }) {
  const session = useSession();

  const [donationAmount, setDonationAmount] = useState<string>('');
  const [donating, setDonating] = useState(false);
  const [donationError, setDonationError] = useState<string | null>(null);

  const handleDonate = async () => {
    setDonationError(null);

    const amountNum = parseFloat(donationAmount);
    if (isNaN(amountNum) || amountNum <= 0) {
      setDonationError('Please enter a valid donation amount.');
      return;
    }

    setDonating(true);
    try {
      const response = await fetch(`${process.env.PUBLIC_BACKEND_API}/campaigns/${id}/donations`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/ld+json',
          Authorization: `Bearer ${session.token}`,
        },
        body: JSON.stringify({
          amount: amountNum,
          message: 'Happy wishes!',
          paymentMethod: 'dummy'
        }),
      });

      if (!response.ok) {
        throw new Error('Donation failed');
      }

      const newDonation = await response.json();
      setDonationAmount('');
      onSuccess();
    } catch (err: any) {
      setDonationError(err.message || 'Failed to donate');
    } finally {
      setDonating(false);
    }
  };

  return (
    <>
      {/* Donation Form */}
      <Divider my="md" label="Donate" labelPosition="center" />
      <Group align="flex-end">
        <TextInput
          label="Amount"
          placeholder="e.g. 50"
          value={donationAmount}
          onChange={(event) => setDonationAmount(event.currentTarget.value)}
          type="number"
          min="1"
        />
        <Button loading={donating} onClick={handleDonate}>
          Donate
        </Button>
      </Group>
      {donationError && <Alert mt="sm" color="red">{donationError}</Alert>}
    </>
  );
}

import { useSession } from '@/app/context/SessionContext';
import { Anchor, Flex, Grid, Loader, Text, Title } from '@mantine/core';
import { useEffect, useState } from 'react';
import { CampaignCard } from './CampaignCard';

export function CampaignsList() {
  const session = useSession();
  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchItems = async () => {
      try {
        const response = await fetch(`${process.env.PUBLIC_BACKEND_API}/campaigns`, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/ld+json',
            Authorization: `Bearer ${session.token}`,
          },
        });

        if (!response.ok) {
          throw new Error(`Error ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();
        setItems(data);
      } catch (err: any) {
        setError(err.message || 'Something went wrong');
      } finally {
        setLoading(false);
      }
    };

    fetchItems();
  }, []);

  if (loading) return <Flex justify={'center'}><Loader size={30} /></Flex>;
  if (error) return <p>Error: {error}</p>;

  return (
    <>
      <Grid>
      {items.member.map((item, key) => (
          <Grid.Col span={6} key={key}>
            <CampaignCard item={item} />
          </Grid.Col>
        ))}
      </Grid>
    </>
  );
}

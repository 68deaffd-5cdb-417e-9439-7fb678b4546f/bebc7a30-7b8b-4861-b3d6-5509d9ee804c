'use client'

import { HeaderMegaMenu } from '@/components/Header/HeaderMegaMenu';
import { CampaignsList } from '@/components/Campaigns/CampaignsList';
import { Container } from '@mantine/core';

export default function HomePage() {
  return (
    <>
      
      <HeaderMegaMenu />
      <Container size="xxl">
        <CampaignsList />
      </Container>
    </>
  );
}

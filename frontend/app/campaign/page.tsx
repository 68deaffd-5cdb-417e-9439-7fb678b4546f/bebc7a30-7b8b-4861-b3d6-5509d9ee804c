
import { HeaderMegaMenu } from '@/components/Header/HeaderMegaMenu';
import { Container } from '@mantine/core';
import { CampaignView } from '@/components/Campaigns/CampaignView';
import { CampaignForm } from '@/components/Campaigns/CampaignForm';

export default async function Page() {
  return (
    <>
      <HeaderMegaMenu />
      <Container size="xxl">
        <CampaignForm />
      </Container>
    </>
  );
}

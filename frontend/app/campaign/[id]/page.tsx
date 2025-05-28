
import { HeaderMegaMenu } from '@/components/Header/HeaderMegaMenu';
import { Container } from '@mantine/core';
import { CampaignView } from '@/components/Campaigns/CampaignView';

export default async function Page({
  params,
}: {
  params: Promise<{ id: string }>
}) {
  const { id } = await params

  return (
    <>
      <HeaderMegaMenu />
      <Container size="xxl">
        <CampaignView id={id} />
      </Container>
    </>
  );
}

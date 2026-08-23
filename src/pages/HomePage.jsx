import React from 'react';
import { HeroBanner } from '../components/home/HeroBanner';
import { CategoryPills } from '../components/home/CategoryPills';
import { BestsellerTabs } from '../components/home/BestsellerTabs';
import { FlashSaleBanner } from '../components/home/FlashSaleBanner';
import { BrandStory } from '../components/home/BrandStory';
import { Testimonials } from '../components/home/Testimonials';
import { LookbookGrid } from '../components/home/LookbookGrid';

export const HomePage = () => {
  return (
    <div className="space-y-0 animate-fade-in">
      <HeroBanner />
      <CategoryPills />
      <BestsellerTabs />
      <FlashSaleBanner />
      <BrandStory />
      <Testimonials />
      <LookbookGrid />
    </div>
  );
};

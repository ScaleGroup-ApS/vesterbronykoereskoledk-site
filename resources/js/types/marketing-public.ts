export type MarketingHomeCopyProps = {
    id: number;
    key: string;
    hero_headline_prefix: string;
    hero_headline_accent: string;
    hero_subtitle: string | null;
    why_title: string;
    why_lead: string | null;
    reviews_title: string;
    reviews_lead: string | null;
    reviews_footnote: string | null;
    explore_title: string;
    explore_lead: string | null;
    cta_title: string;
    cta_lead: string | null;
};

export type MarketingValueBlockProps = {
    id: number;
    title: string;
    body: string;
    icon: string;
    sort_order: number;
    is_active: boolean;
};

export type MarketingTestimonialProps = {
    id: number;
    quote: string;
    author_name: string;
    author_detail: string | null;
    sort_order: number;
    is_active: boolean;
};

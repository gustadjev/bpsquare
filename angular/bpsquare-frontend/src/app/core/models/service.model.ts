export interface Service {
  id: number;
  title: string;
  summary: string;
  details: string[];
  icon: string;
  ctaLabel: string;
  ctaUrl: string;
  thumbnail: string | null;
}

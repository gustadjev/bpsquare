export interface CaseStudy {
  id: number;
  title: string;
  problem: string;
  solution: string;
  technologies: string[];
  outcome: string;
  thumbnail: string | null;
}

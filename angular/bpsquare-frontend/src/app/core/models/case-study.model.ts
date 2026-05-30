export interface CaseStudy {
  id: number;
  title: string;
  status?: 'concept' | 'volunteer' | 'in_progress' | 'completed';
  clientType?: string;
  problem: string;
  constraints?: string;
  solution: string;
  technologies: string[];
  outcome: string;
  lessonsLearned?: string;
  isConfidential?: boolean;
  thumbnail: string | null;
}

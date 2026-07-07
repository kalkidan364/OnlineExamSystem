export interface StudentProfile {
  name: string;
  id: string;
  email: string;
  department: string;
  program: string;
  semester: string;
  academicYear: string;
  avatar: string;
  cgpa: number;
  creditsCompleted: number;
}

export interface Question {
  id: number;
  text: string;
  instruction?: string;
  options?: string[];
  pairs?: { left: string, right: string }[];
  columnA?: string;
  columnB?: string;
  type: 'multiple-choice' | 'text' | 'true_false' | 'multiple_true_false' | 'fill_blank' | 'matching' | string;
  correctAnswer?: string;
  selectedAnswer?: string;
  flagged?: boolean;
}

export interface ActiveExam {
  id: number;
  courseCode: string;
  courseName: string;
  examTitle: string;
  instructor: string;
  date: string;
  time: string;
  durationMinutes: number;
  totalQuestions: number;
  totalMarks: number;
  settings?: Record<string, any>;
  questions: Question[];
}

export interface UpcomingExam {
  id: number;
  courseCode: string;
  courseName: string;
  instructor: string;
  examType: string;
  scheduledDate: string;
  startTime: string;
  durationMinutes: number;
  totalQuestions: number;
  totalMarks: number;
  status: 'Soon' | 'Pending' | 'Ready';
}

export interface RecentResult {
  id: number;
  courseCode: string;
  courseName: string;
  examTitle: string;
  score: number;
  totalMarks: number;
  percentage: number;
  grade: 'A+' | 'A' | 'A-' | 'B+' | 'B' | 'C+' | 'C';
  status: 'Passed' | 'Failed';
  completedDate: string;
  questionsReview: {
    questionText: string;
    studentAnswer: string;
    correctAnswer: string;
    explanation: string;
    isCorrect: boolean;
  }[];
}

export interface Announcement {
  id: string;
  title: string;
  content: string;
  date: string;
  category: 'Schedule' | 'Notice' | 'System' | 'Urgent';
  sender: string;
}

export interface CalendarEvent {
  id: string;
  title: string;
  date: string;
  type: 'Exam' | 'Holiday' | 'Deadline' | 'Academic';
  description: string;
}

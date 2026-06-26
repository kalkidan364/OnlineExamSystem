import { useState, useEffect } from 'react';
import { initialStudentProfile, sampleActiveExam, sampleUpcomingExams, sampleRecentResults, sampleAnnouncements, sampleCalendarEvents } from './data/mockData';
import { StudentProfile, ActiveExam, UpcomingExam, RecentResult } from './types';

// Component imports
import Header from './components/Header';
import HeroSection from './components/HeroSection';
import ActiveExamCard from './components/ActiveExamCard';
import UpcomingExams from './components/UpcomingExams';
import ProgressStats from './components/ProgressStats';
import RecentResults from './components/RecentResults';
import Announcements from './components/Announcements';
import CalendarSection from './components/CalendarSection';
import QuickActions from './components/QuickActions';
import ExamConsole from './components/ExamConsole';
import ResultReviewModal from './components/ResultReviewModal';
import DownloadTranscriptModal from './components/DownloadTranscriptModal';
import ProfileModal from './components/ProfileModal';

// Lucide icon imports
import { ShieldAlert, Info, X, Bell, BookOpen, Clock, CalendarDays, KeyRound, CheckSquare } from 'lucide-react';

export default function App() {
  // Navigation & Screen routing state
  const [currentView, setCurrentView] = useState<'dashboard' | 'exam-console'>('dashboard');

  // Core Dynamic entities states
  const [profile, setProfile] = useState<StudentProfile>(initialStudentProfile);
  const [results, setResults] = useState<RecentResult[]>(sampleRecentResults);
  const [activeExam, setActiveExam] = useState<ActiveExam | null>(sampleActiveExam);
  const [upcomingExams, setUpcomingExams] = useState<UpcomingExam[]>(sampleUpcomingExams);

  // Overlay controller states
  const [selectedResultForReview, setSelectedResultForReview] = useState<RecentResult | null>(null);
  const [isNotificationsOpen, setIsNotificationsOpen] = useState<boolean>(false);
  const [isTranscriptOpen, setIsTranscriptOpen] = useState<boolean>(false);
  const [isProfileOpen, setIsProfileOpen] = useState<boolean>(false);
  const [upcomingGuidelines, setUpcomingGuidelines] = useState<UpcomingExam | null>(null);

  // Stats calculation (dynamic updates!)
  const completedCount = results.length;
  const remainingCount = upcomingExams.length + (activeExam ? 1 : 0);
  
  // Calculate GPA based on existing results
  const averageScore = Math.round(results.reduce((acc, curr) => acc + curr.percentage, 0) / results.length);
  const passRate = 100; // Hardcoded simulation for simplicity

  // Action helper when the student submits an exam in ExamConsole
  const handleExamCompleted = (
    answers: Record<number, string>,
    scoredMarks: number,
    percentage: number
  ) => {
    if (!activeExam) return;

    // Map questions for review Modal
    const mappedReview = activeExam.questions.map((q) => {
      const studentAns = answers[q.id] || "No Answer Supplied";
      return {
        questionText: q.text,
        studentAnswer: studentAns,
        correctAnswer: q.correctAnswer || "Refer to department lecture outline",
        explanation: q.type === 'multiple-choice' 
          ? `The correct answer is indeed option '${q.correctAnswer}'. Refer to chapter 3 on system replication strategies.`
          : `Teacher review pending. Evaluated as correct based on technical depth. Details: ${studentAns.substring(0, 30)}...`,
        isCorrect: q.type === 'multiple-choice' ? studentAns === q.correctAnswer : studentAns.trim().length > 10
      };
    });

    // Create a new RecentResult object
    const letterGrade: 'A+' | 'A' | 'A-' | 'B+' | 'B' | 'C+' | 'C' = 
      percentage >= 95 ? 'A+' :
      percentage >= 90 ? 'A' :
      percentage >= 85 ? 'A-' :
      percentage >= 80 ? 'B+' :
      percentage >= 75 ? 'B' :
      percentage >= 65 ? 'C+' : 'C';

    const newResult: RecentResult = {
      id: activeExam.id,
      courseCode: activeExam.courseCode,
      courseName: activeExam.courseName,
      examTitle: activeExam.examTitle,
      score: scoredMarks,
      totalMarks: activeExam.totalMarks,
      percentage: percentage,
      grade: letterGrade,
      status: 'Passed',
      completedDate: new Date().toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }),
      questionsReview: mappedReview
    };

    // Prepend new result to results
    setResults(prev => [newResult, ...prev]);

    // Clear active exam to simulate finished status
    setActiveExam(null);

    // Dynamically bump student profile metrics!
    setProfile(prev => ({
      ...prev,
      creditsCompleted: prev.creditsCompleted + 5, // 5 credits earned per course
      cgpa: Math.min(4.00, Number((prev.cgpa + 0.02).toFixed(2))) // Bump CGPA as a nice visual award
    }));

    // Route back to dashboard
    setCurrentView('dashboard');

    // Trigger congratulations modal / notification
    setTimeout(() => {
      alert(`CONGRATULATIONS!\nYou have completed: ${activeExam.courseName} successfully!\nYour score is ${percentage}% (${letterGrade}). Your CGPA has been adjusted to ${profile.cgpa + 0.02} and 5 credits have been officially recorded.`);
    }, 400);
  };

  // Quick Action Click Router
  const handleQuickAction = (actionKey: 'take-exam' | 'view-results' | 'download-results' | 'update-profile' | 'academic-calendar') => {
    switch (actionKey) {
      case 'take-exam':
        if (activeExam) {
          setCurrentView('exam-console');
        } else {
          alert("All current scheduled examinations have been completed. Please check back next week.");
        }
        break;
      case 'view-results':
        // Smoothly scroll to the results section
        document.getElementById('recent-results-section')?.scrollIntoView({ behavior: 'smooth' });
        break;
      case 'download-results':
        setIsTranscriptOpen(true);
        break;
      case 'update-profile':
        setIsProfileOpen(true);
        break;
      case 'academic-calendar':
        document.getElementById('calendar-section')?.scrollIntoView({ behavior: 'smooth' });
        break;
    }
  };

  return (
    <div className="min-h-screen bg-slate-50 font-sans text-slate-800 antialiased selection:bg-indigo-500 selection:text-white pb-16">
      
      {/* 1. RENDER LIVE EXAM CONSOLE (FULLSCREEN OVERLAY) */}
      {currentView === 'exam-console' && activeExam ? (
        <ExamConsole
          exam={activeExam}
          onCancel={() => setCurrentView('dashboard')}
          onSubmitExam={handleExamCompleted}
        />
      ) : (
        
        /* 2. RENDER MAIN PORTAL DASHBOARD */
        <div className="space-y-8 animate-in fade-in duration-300">
          
          {/* Header element */}
          <Header
            profile={profile}
            announcements={sampleAnnouncements}
            onOpenProfile={() => setIsProfileOpen(true)}
            onOpenNotifications={() => setIsNotificationsOpen(true)}
          />

          {/* Core container centering the grid */}
          <main className="mx-auto max-w-7xl px-6 space-y-8">
            
            {/* Row 1: Academic Hero Welcome Section */}
            <HeroSection profile={profile} />

            {/* Row 2: Secondary Quick Actions Rail */}
            <QuickActions onAction={handleQuickAction} />

            {/* Row 3: Primary Core Grid Layout (Active Exam vs. Upcoming Exams) */}
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
              
              {/* Left Column (Active Exam Area - Dominates with 7 cols) */}
              <div className="lg:col-span-7 space-y-8">
                {activeExam ? (
                  <div>
                    <div className="mb-4">
                      <h3 className="text-lg font-bold text-slate-900">Current Evaluation Target</h3>
                      <p className="text-xs text-slate-500 font-medium">Sit for your scheduled paper before the portal closes</p>
                    </div>
                    <ActiveExamCard
                      exam={activeExam}
                      onStartExam={() => setCurrentView('exam-console')}
                    />
                  </div>
                ) : (
                  <div className="rounded-2xl border-2 border-dashed border-emerald-200 bg-emerald-50/20 p-8 text-center space-y-4">
                    <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                      <CheckSquare className="h-6 w-6" />
                    </div>
                    <div>
                      <h4 className="text-base font-bold text-slate-950">You Are Fully Up-to-Date!</h4>
                      <p className="text-xs text-slate-600 mt-1 max-w-md mx-auto leading-relaxed">
                        Excellent job! You have submitted all active examination papers for the Semester II syllabus. Your responses are stored securely in the Wollo University main record server.
                      </p>
                    </div>
                  </div>
                )}

                {/* Upcoming exams list */}
                <UpcomingExams
                  exams={upcomingExams}
                  onStartExam={(examId) => {
                    const found = upcomingExams.find(ex => ex.id === examId);
                    if (found) {
                      // Promote to active exam simulation
                      const promotedExam: ActiveExam = {
                        id: found.id,
                        courseCode: found.courseCode,
                        courseName: found.courseName,
                        examTitle: found.examType,
                        instructor: found.instructor,
                        date: "Today",
                        time: found.startTime,
                        durationMinutes: found.durationMinutes,
                        totalQuestions: 5,
                        totalMarks: found.totalMarks,
                        questions: sampleActiveExam.questions // Reuse same high-fidelity questions
                      };
                      setActiveExam(promotedExam);
                      // Remove from upcoming list
                      setUpcomingExams(prev => prev.filter(ex => ex.id !== examId));
                      // Scroll up to active exam
                      window.scrollTo({ top: 0, behavior: 'smooth' });
                      alert(`PROMOTED:\n${found.courseName} is now ready in your primary focus card below.`);
                    }
                  }}
                  onViewDetails={(exam) => setUpcomingGuidelines(exam)}
                />
              </div>

              {/* Right Column (Side-rails for secondary information - 5 cols) */}
              <div className="lg:col-span-5 space-y-8">
                
                {/* Visual Calendar Component */}
                <div id="calendar-section">
                  <CalendarSection events={sampleCalendarEvents} />
                </div>

                {/* Official Announcements */}
                <Announcements announcements={sampleAnnouncements} />

              </div>

            </div>

            {/* Row 4: Term Performance & Circular Metrics */}
            <ProgressStats
              completedCount={completedCount}
              remainingCount={remainingCount}
              averageScore={averageScore}
              passRate={passRate}
            />

            {/* Row 5: Detailed Scoreboard Section */}
            <div id="recent-results-section">
              <RecentResults
                results={results}
                onViewResult={(res) => setSelectedResultForReview(res)}
                onDownloadTranscript={() => setIsTranscriptOpen(true)}
              />
            </div>

          </main>

        </div>
      )}

      {/* ----------------- GLOBAL PORTAL OVERLAY DRAWERS & MODALS ----------------- */}

      {/* A. Result Review Modal (Displays detailed answers/explanations) */}
      {selectedResultForReview && (
        <ResultReviewModal
          result={selectedResultForReview}
          onClose={() => setSelectedResultForReview(null)}
        />
      )}

      {/* B. Official printable transcript Record slip */}
      {isTranscriptOpen && (
        <DownloadTranscriptModal
          profile={profile}
          results={results}
          onClose={() => setIsTranscriptOpen(false)}
        />
      )}

      {/* C. Registered profile credentials editor */}
      {isProfileOpen && (
        <ProfileModal
          profile={profile}
          onClose={() => setIsProfileOpen(false)}
          onUpdateProfile={(updated) => {
            setProfile(updated);
            alert("SUCCESS:\nYour registry credentials have been updated successfully on the secure server.");
          }}
        />
      )}

      {/* D. Upcoming Exam Guidelines drawer */}
      {upcomingGuidelines && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs animate-in fade-in">
          <div className="relative w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl">
            <button
              onClick={() => setUpcomingGuidelines(null)}
              className="absolute top-4 right-4 rounded-xl p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors"
            >
              <X className="h-5 w-5" />
            </button>

            <div className="flex items-center gap-3 mb-4">
              <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600 border border-amber-100">
                <Info className="h-5 w-5" />
              </div>
              <div>
                <span className="text-[9px] font-mono font-bold text-slate-400 uppercase tracking-widest">{upcomingGuidelines.courseCode}</span>
                <h3 className="text-sm font-black text-slate-900">Guidelines & Scope</h3>
              </div>
            </div>

            <h4 className="text-xs font-bold text-slate-800">{upcomingGuidelines.courseName}</h4>
            <p className="text-xs text-slate-500 mt-1">Instructor: {upcomingGuidelines.instructor}</p>

            <hr className="my-4 border-slate-100" />

            <div className="space-y-3 font-sans text-xs text-slate-600 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100">
              <p className="font-bold text-slate-900">Syllabus Chapters Covered:</p>
              <ul className="list-disc list-inside space-y-1.5 pl-1">
                <li>Lecture units 1-5 (Core theory & diagnostics)</li>
                <li>Performance metrics and case evaluations</li>
                <li>Design patterns and automated unit suites</li>
              </ul>
              <p className="font-bold text-slate-900 mt-3">Expected Deliverables:</p>
              <p className="pl-1">
                20 Multiple choice items (1 mark each) and 2 logical essays (5 marks each). Strict proctoring restrictions apply.
              </p>
            </div>

            <button
              onClick={() => setUpcomingGuidelines(null)}
              className="mt-6 w-full rounded-xl bg-slate-900 py-3 text-xs font-bold text-white hover:bg-slate-800 transition-colors"
            >
              Acknowledge & Close
            </button>
          </div>
        </div>
      )}

      {/* E. Floating live notifications drawer */}
      {isNotificationsOpen && (
        <div className="fixed inset-y-0 right-0 z-50 w-full max-w-md bg-white shadow-2xl border-l border-slate-200 flex flex-col justify-between animate-in slide-in-from-right duration-250">
          <div className="p-6 overflow-y-auto space-y-6">
            <div className="flex items-center justify-between border-b border-slate-100 pb-4">
              <div className="flex items-center gap-2">
                <Bell className="h-5 w-5 text-indigo-600 animate-bounce" />
                <h3 className="text-base font-black text-slate-900">Active Notifications</h3>
              </div>
              <button
                onClick={() => setIsNotificationsOpen(false)}
                className="rounded-xl p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            {/* Simulated Alerts list */}
            <div className="space-y-4 font-sans text-xs text-slate-600 leading-relaxed">
              <div className="bg-rose-50 border border-rose-200 rounded-xl p-4 space-y-1.5">
                <p className="font-bold text-rose-800 flex items-center gap-1">
                  <ShieldAlert className="h-4 w-4 shrink-0 text-rose-600" />
                  Live Proctoring Alert
                </p>
                <p className="text-rose-700">
                  Ensure your microphone is calibrated. The system logs background noises during the live assessment window.
                </p>
              </div>

              <div className="bg-indigo-50 border border-indigo-200 rounded-xl p-4 space-y-1.5">
                <p className="font-bold text-indigo-800">Candidacy Cleared</p>
                <p className="text-indigo-700">
                  Your registration status for course **Compiler Design (SWE-422)** has been cleared by the Registrar.
                </p>
              </div>

              <div className="bg-amber-50 border border-amber-200 rounded-xl p-4 space-y-1.5">
                <p className="font-bold text-amber-800">Senate Warning</p>
                <p className="text-amber-700">
                  All make-up examinations must be applied for within 48 hours of schedule release. No exceptions.
                </p>
              </div>
            </div>
          </div>

          <div className="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-center">
            <button
              onClick={() => setIsNotificationsOpen(false)}
              className="w-full rounded-xl bg-slate-900 hover:bg-slate-800 py-3 text-xs font-bold text-white transition-colors"
            >
              Close Alerts Drawer
            </button>
          </div>
        </div>
      )}

    </div>
  );
}

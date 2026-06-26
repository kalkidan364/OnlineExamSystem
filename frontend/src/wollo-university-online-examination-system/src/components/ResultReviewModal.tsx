import React from 'react';
import { X, CheckCircle, AlertTriangle, AlertCircle, Bookmark, GraduationCap, Clock } from 'lucide-react';
import { RecentResult } from '../types';

interface ResultReviewModalProps {
  result: RecentResult;
  onClose: () => void;
}

export default function ResultReviewModal({ result, onClose }: ResultReviewModalProps) {
  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs">
      <div className="relative w-full max-w-3xl rounded-2xl border border-slate-200 bg-white shadow-2xl animate-in fade-in zoom-in-95 duration-150 flex flex-col max-h-[90vh]">
        
        {/* Header section with university details */}
        <header className="border-b border-slate-100 p-6 flex items-center justify-between bg-slate-50 rounded-t-2xl">
          <div className="flex items-center gap-3">
            <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-100">
              <GraduationCap className="h-5 w-5" />
            </div>
            <div>
              <p className="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{result.courseCode}</p>
              <h3 className="text-base font-black text-slate-900">{result.courseName}</h3>
              <p className="text-xs text-slate-500 font-medium">Evaluation: {result.examTitle}</p>
            </div>
          </div>
          
          <button
            onClick={onClose}
            className="rounded-xl p-1.5 text-slate-400 hover:bg-slate-200 hover:text-slate-600 transition-colors"
          >
            <X className="h-5 w-5" />
          </button>
        </header>

        {/* Scoring summary card */}
        <div className="p-6 bg-indigo-900 text-white flex flex-col md:flex-row justify-between items-center gap-4">
          <div>
            <span className="text-[10px] uppercase font-bold tracking-widest text-indigo-300">STUDENT SCORE SHEET</span>
            <div className="flex items-baseline gap-2 mt-1">
              <span className="text-4xl font-black font-mono">{result.score}</span>
              <span className="text-lg text-indigo-300 font-mono">/{result.totalMarks} Marks</span>
              <span className="text-xs font-semibold bg-white/10 px-2 py-0.5 rounded-md ml-3 text-emerald-400">
                {result.percentage}% Score Rate
              </span>
            </div>
            <p className="text-xs text-slate-300 mt-1">Completed and logged officially on {result.completedDate}</p>
          </div>

          <div className="flex items-center gap-4 border-t border-indigo-800 md:border-t-0 pt-4 md:pt-0">
            <div className="text-center bg-white/10 px-4 py-2 rounded-xl border border-white/10">
              <span className="text-[9px] uppercase font-bold text-indigo-200 block">Grade Issued</span>
              <span className="text-2xl font-black font-mono text-white">{result.grade}</span>
            </div>
            <div className="text-center bg-emerald-500/10 px-4 py-2 rounded-xl border border-emerald-500/20">
              <span className="text-[9px] uppercase font-bold text-emerald-300 block">Outcome</span>
              <span className="text-sm font-extrabold text-emerald-400 uppercase">PASSED</span>
            </div>
          </div>
        </div>

        {/* Question-by-Question breakdown list (scrollable) */}
        <div className="flex-1 overflow-y-auto p-6 space-y-6">
          <div className="border-b border-slate-100 pb-3">
            <h4 className="text-xs font-bold text-slate-400 uppercase tracking-wider">Academic Response Review</h4>
            <p className="text-xs text-slate-500 mt-0.5">Below are the questions, your selected answers, and explanations.</p>
          </div>

          <div className="space-y-6">
            {result.questionsReview.map((review, idx) => (
              <div
                key={idx}
                className={`rounded-xl border p-5 space-y-4 ${
                  review.isCorrect 
                    ? 'border-emerald-100 bg-emerald-50/10' 
                    : 'border-rose-100 bg-rose-50/10'
                }`}
              >
                {/* Header row of card */}
                <div className="flex items-start justify-between gap-3">
                  <span className="font-mono text-xs font-extrabold text-slate-500">
                    Question #{idx + 1}
                  </span>
                  
                  {review.isCorrect ? (
                    <span className="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2.5 py-0.5 rounded-full border border-emerald-200 uppercase">
                      <CheckCircle className="h-3.5 w-3.5" />
                      <span>Correct (+10 pts)</span>
                    </span>
                  ) : (
                    <span className="inline-flex items-center gap-1 text-[10px] font-bold text-rose-700 bg-rose-100 px-2.5 py-0.5 rounded-full border border-rose-200 uppercase">
                      <AlertCircle className="h-3.5 w-3.5" />
                      <span>Incorrect (0 pts)</span>
                    </span>
                  )}
                </div>

                {/* Question Statement */}
                <p className="text-xs font-bold text-slate-800 leading-relaxed font-sans">
                  {review.questionText}
                </p>

                {/* Answers block */}
                <div className="grid gap-3 text-xs sm:grid-cols-2">
                  <div className="rounded-lg bg-white p-3 border border-slate-200">
                    <span className="text-[9px] uppercase font-bold text-slate-400 block mb-1">Your Submitted Response:</span>
                    <p className={`font-medium ${review.isCorrect ? 'text-emerald-700' : 'text-rose-600'}`}>
                      {review.studentAnswer}
                    </p>
                  </div>

                  <div className="rounded-lg bg-white p-3 border border-slate-200">
                    <span className="text-[9px] uppercase font-bold text-slate-400 block mb-1">Correct Answer:</span>
                    <p className="font-medium text-slate-800">
                      {review.correctAnswer}
                    </p>
                  </div>
                </div>

                {/* Instructor explanation log */}
                <div className="rounded-lg bg-slate-50 p-3.5 border border-slate-200 text-xs text-slate-600 leading-relaxed">
                  <span className="text-[9px] uppercase font-bold text-slate-400 block mb-1 flex items-center gap-1">
                    <Bookmark className="h-3.5 w-3.5 text-indigo-500" />
                    Grading Explanation
                  </span>
                  <p className="font-sans">
                    {review.explanation}
                  </p>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Footer section */}
        <footer className="border-t border-slate-100 p-4 flex justify-between items-center bg-slate-50 rounded-b-2xl">
          <p className="text-[10px] font-semibold text-slate-400 flex items-center gap-1.5">
            <Clock className="h-3.5 w-3.5" />
            <span>Audit Ref: SEC-93-WULLO-2026</span>
          </p>
          <button
            onClick={onClose}
            className="rounded-xl bg-slate-900 hover:bg-slate-800 px-5 py-2 text-xs font-bold text-white transition-colors"
          >
            Finished Reviewing
          </button>
        </footer>

      </div>
    </div>
  );
}

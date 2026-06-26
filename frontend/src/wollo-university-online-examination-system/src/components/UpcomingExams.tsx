import React from 'react';
import { Calendar, Clock, User, ArrowRight, Hourglass } from 'lucide-react';
import { UpcomingExam } from '../types';

interface UpcomingExamsProps {
  exams: UpcomingExam[];
  onStartExam: (examId: string) => void;
  onViewDetails: (exam: UpcomingExam) => void;
}

export default function UpcomingExams({ exams, onStartExam, onViewDetails }: UpcomingExamsProps) {
  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <div>
          <h3 className="text-lg font-bold text-slate-900">Upcoming Academic Schedule</h3>
          <p className="text-xs text-slate-500 font-medium">Examinations scheduled for your registration code</p>
        </div>
        <span className="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 border border-slate-200">
          {exams.length} Scheduled
        </span>
      </div>

      <div className="grid gap-4 md:grid-cols-1 lg:grid-cols-2">
        {exams.map((exam) => {
          const isReady = exam.status === 'Ready';
          return (
            <div
              key={exam.id}
              className={`group relative overflow-hidden rounded-xl border bg-white p-5 transition-all hover:shadow-md hover:border-slate-300 ${
                isReady ? 'ring-2 ring-emerald-500/10 border-emerald-200' : 'border-slate-200'
              }`}
            >
              {/* Corner badge indicating status */}
              <div className="flex items-center justify-between mb-3">
                <span className="font-mono text-xs font-bold text-slate-500 uppercase tracking-wider">
                  {exam.courseCode}
                </span>
                <span
                  className={`inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-bold ${
                    exam.status === 'Ready'
                      ? 'bg-emerald-50 text-emerald-700 border border-emerald-100'
                      : exam.status === 'Pending'
                      ? 'bg-amber-50 text-amber-700 border border-amber-100'
                      : 'bg-slate-50 text-slate-600 border border-slate-200'
                  }`}
                >
                  <span className={`h-1.5 w-1.5 rounded-full ${
                    exam.status === 'Ready' ? 'bg-emerald-500' : exam.status === 'Pending' ? 'bg-amber-500' : 'bg-slate-400'
                  }`} />
                  {exam.status === 'Ready' ? 'Ready to Start' : exam.status === 'Pending' ? 'In Review' : 'Scheduled'}
                </span>
              </div>

              {/* Course Title and Type */}
              <h4 className="font-bold text-slate-800 text-sm group-hover:text-indigo-600 transition-colors">
                {exam.courseName}
              </h4>
              <p className="text-xs text-slate-500 font-medium mt-0.5">{exam.examType}</p>

              {/* Schedule Parameters */}
              <div className="mt-4 grid grid-cols-2 gap-3 text-xs text-slate-600 border-t border-slate-50 pt-3">
                <div className="flex items-center gap-1.5">
                  <Calendar className="h-3.5 w-3.5 text-slate-400" />
                  <span className="font-medium truncate">{exam.scheduledDate}</span>
                </div>
                <div className="flex items-center gap-1.5">
                  <Clock className="h-3.5 w-3.5 text-slate-400" />
                  <span className="font-medium truncate">{exam.startTime} ({exam.durationMinutes}m)</span>
                </div>
                <div className="flex items-center gap-1.5 col-span-2">
                  <User className="h-3.5 w-3.5 text-slate-400" />
                  <span className="font-medium">Lecturer: {exam.instructor}</span>
                </div>
              </div>

              {/* Action Buttons */}
              <div className="mt-4 flex gap-2">
                {isReady ? (
                  <button
                    onClick={() => onStartExam(exam.id)}
                    className="flex-1 rounded-lg bg-emerald-600 py-2 text-xs font-bold text-white transition-colors hover:bg-emerald-700 active:scale-[0.99]"
                  >
                    Start Exam
                  </button>
                ) : (
                  <button
                    disabled
                    className="flex-1 rounded-lg bg-slate-100 py-2 text-xs font-bold text-slate-400 cursor-not-allowed border border-slate-200"
                  >
                    Locked
                  </button>
                )}
                <button
                  onClick={() => onViewDetails(exam)}
                  className="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-600 transition-colors hover:bg-slate-50 active:bg-slate-100"
                  title="View Guidelines"
                >
                  Details
                </button>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
}

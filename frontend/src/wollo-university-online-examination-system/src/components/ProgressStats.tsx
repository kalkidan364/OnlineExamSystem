import React from 'react';
import { Target, CheckCircle2, Award, CalendarClock, ShieldCheck } from 'lucide-react';

interface ProgressStatsProps {
  completedCount: number;
  remainingCount: number;
  averageScore: number;
  passRate: number;
}

export default function ProgressStats({ completedCount, remainingCount, averageScore, passRate }: ProgressStatsProps) {
  // SVG Circular progress configurations
  const radius = 30;
  const circumference = 2 * Math.PI * radius;

  const renderCircularIndicator = (percentage: number, colorClass: string, strokeBg: string) => {
    const strokeDashoffset = circumference - (percentage / 100) * circumference;
    return (
      <div className="relative flex h-16 w-16 items-center justify-center">
        <svg className="absolute top-0 left-0 h-16 w-16 -rotate-90">
          {/* Background circle */}
          <circle
            cx="32"
            cy="32"
            r={radius}
            className={`${strokeBg} stroke-current`}
            strokeWidth="5"
            fill="transparent"
          />
          {/* Foreground progress */}
          <circle
            cx="32"
            cy="32"
            r={radius}
            className={`${colorClass} stroke-current transition-all duration-500`}
            strokeWidth="5"
            strokeDasharray={circumference}
            strokeDashoffset={strokeDashoffset}
            strokeLinecap="round"
            fill="transparent"
          />
        </svg>
        <span className="font-mono text-xs font-bold text-slate-800">{percentage}%</span>
      </div>
    );
  };

  return (
    <div className="space-y-4">
      <div>
        <h3 className="text-lg font-bold text-slate-900">Academic Progress Overview</h3>
        <p className="text-xs text-slate-500 font-medium">Real-time status of your term performance</p>
      </div>

      <div className="grid gap-4 grid-cols-2 md:grid-cols-4">
        {/* Card 1: Completed Exams */}
        <div className="rounded-xl border border-slate-200 bg-white p-4 shadow-xs hover:shadow-md transition-shadow">
          <div className="flex items-center justify-between">
            <span className="text-xs font-bold uppercase tracking-wider text-slate-400">Completed</span>
            <div className="rounded-lg bg-indigo-50 p-1.5 text-indigo-600">
              <CheckCircle2 className="h-4 w-4" />
            </div>
          </div>
          <div className="mt-3 flex items-baseline gap-1.5">
            <span className="text-2xl font-black text-slate-900">{completedCount}</span>
            <span className="text-xs font-medium text-slate-400">Courses done</span>
          </div>
          <div className="mt-2 text-[10px] font-semibold text-emerald-600">100% submission rating</div>
        </div>

        {/* Card 2: Remaining Exams */}
        <div className="rounded-xl border border-slate-200 bg-white p-4 shadow-xs hover:shadow-md transition-shadow">
          <div className="flex items-center justify-between">
            <span className="text-xs font-bold uppercase tracking-wider text-slate-400">Remaining</span>
            <div className="rounded-lg bg-amber-50 p-1.5 text-amber-600">
              <CalendarClock className="h-4 w-4" />
            </div>
          </div>
          <div className="mt-3 flex items-baseline gap-1.5">
            <span className="text-2xl font-black text-slate-900">{remainingCount}</span>
            <span className="text-xs font-medium text-slate-400">Scheduled papers</span>
          </div>
          <div className="mt-2 text-[10px] font-semibold text-indigo-600">Active preparation stage</div>
        </div>

        {/* Card 3: Average Score */}
        <div className="rounded-xl border border-slate-200 bg-white p-4 shadow-xs hover:shadow-md transition-shadow flex items-center justify-between gap-2">
          <div>
            <span className="text-xs font-bold uppercase tracking-wider text-slate-400 block">Grade Point</span>
            <div className="mt-2 flex items-baseline gap-1.5">
              <span className="text-2xl font-black text-slate-900">{averageScore}%</span>
            </div>
            <span className="text-[10px] font-semibold text-emerald-600 block mt-1">Excellent (A Grade Average)</span>
          </div>
          {renderCircularIndicator(averageScore, 'text-indigo-600', 'text-slate-100')}
        </div>

        {/* Card 4: Pass Rate */}
        <div className="rounded-xl border border-slate-200 bg-white p-4 shadow-xs hover:shadow-md transition-shadow flex items-center justify-between gap-2">
          <div>
            <span className="text-xs font-bold uppercase tracking-wider text-slate-400 block">Pass Rate</span>
            <div className="mt-2 flex items-baseline gap-1.5">
              <span className="text-2xl font-black text-slate-900">{passRate}%</span>
            </div>
            <span className="text-[10px] font-semibold text-emerald-600 block mt-1">Safe Zone (No failures)</span>
          </div>
          {renderCircularIndicator(passRate, 'text-emerald-500', 'text-slate-100')}
        </div>
      </div>
    </div>
  );
}

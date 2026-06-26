import React, { useState, useEffect } from 'react';
import { Play, Calendar, Users, AlertTriangle, CheckCircle, Flame, ShieldAlert, Cpu } from 'lucide-react';
import { ActiveExam } from '../types';

interface ActiveExamCardProps {
  exam: ActiveExam;
  onStartExam: () => void;
}

export default function ActiveExamCard({ exam, onStartExam }: ActiveExamCardProps) {
  const [timeLeft, setTimeLeft] = useState({ hours: 1, minutes: 31, seconds: 45 });

  // Simulate counting down
  useEffect(() => {
    const timer = setInterval(() => {
      setTimeLeft(prev => {
        if (prev.seconds > 0) {
          return { ...prev, seconds: prev.seconds - 1 };
        } else if (prev.minutes > 0) {
          return { ...prev, minutes: prev.minutes - 1, seconds: 59 };
        } else if (prev.hours > 0) {
          return { hours: prev.hours - 1, minutes: 59, seconds: 59 };
        } else {
          clearInterval(timer);
          return prev;
        }
      });
    }, 1000);

    return () => clearInterval(timer);
  }, []);

  const formatNumber = (num: number) => num.toString().padStart(2, '0');

  return (
    <div className="relative overflow-hidden rounded-2xl border-2 border-indigo-500 bg-white p-6 shadow-xl ring-8 ring-indigo-500/5 transition-all hover:shadow-2xl">
      {/* Top Banner Accent */}
      <div className="absolute top-0 left-0 right-0 h-1.5 bg-linear-to-r from-indigo-500 via-purple-500 to-pink-500"></div>

      {/* Live Exam Header Status */}
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-2">
          <span className="relative flex h-3 w-3">
            <span className="absolute inline-flex h-full w-full animate-ping rounded-full bg-rose-400 opacity-75"></span>
            <span className="relative inline-flex h-3 w-3 rounded-full bg-rose-500"></span>
          </span>
          <span className="text-xs font-extrabold uppercase tracking-widest text-rose-600">LIVE EXAM WINDOW OPEN</span>
        </div>
        <span className="rounded-md bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700 border border-indigo-100">
          Weight: 50% of Grade
        </span>
      </div>

      {/* Exam Name & Course */}
      <div className="mt-5">
        <p className="text-sm font-mono font-bold tracking-wider text-indigo-600 uppercase">
          {exam.courseCode}
        </p>
        <h2 className="mt-1 text-2xl font-black leading-tight tracking-tight text-slate-900 md:text-3xl">
          {exam.courseName}
        </h2>
        <p className="mt-1.5 text-sm font-medium text-slate-500">
          Evaluation: <span className="text-slate-800 font-semibold">{exam.examTitle}</span>
        </p>
      </div>

      <hr className="my-5 border-slate-100" />

      {/* Exam Parameters Grid */}
      <div className="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div className="rounded-xl bg-slate-50 p-3 border border-slate-100">
          <p className="text-[10px] font-bold uppercase tracking-wider text-slate-400">Instructor</p>
          <p className="mt-0.5 text-xs font-bold text-slate-800 truncate">{exam.instructor}</p>
        </div>
        <div className="rounded-xl bg-slate-50 p-3 border border-slate-100">
          <p className="text-[10px] font-bold uppercase tracking-wider text-slate-400">Duration</p>
          <p className="mt-0.5 text-xs font-bold text-slate-800">{exam.durationMinutes} Minutes</p>
        </div>
        <div className="rounded-xl bg-slate-50 p-3 border border-slate-100">
          <p className="text-[10px] font-bold uppercase tracking-wider text-slate-400">Format</p>
          <p className="mt-0.5 text-xs font-bold text-slate-800">{exam.totalQuestions} Questions</p>
        </div>
        <div className="rounded-xl bg-slate-50 p-3 border border-slate-100">
          <p className="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Marks</p>
          <p className="mt-0.5 text-xs font-bold text-slate-800">{exam.totalMarks} Marks</p>
        </div>
      </div>

      {/* Interactive Countdown block */}
      <div className="mt-6 rounded-2xl bg-slate-950 p-5 text-white border border-slate-800">
        <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <p className="text-xs font-bold text-indigo-400 uppercase tracking-widest flex items-center gap-1.5">
              <Flame className="h-4 w-4 text-orange-500 animate-pulse" />
              Portal Access Timer
            </p>
            <p className="mt-0.5 text-xs text-slate-400">You must submit before this clock hits zero.</p>
          </div>
          <div className="flex items-center gap-2">
            {/* Ticking Units */}
            <div className="flex flex-col items-center">
              <span className="rounded-lg bg-white/10 px-3 py-1 font-mono text-xl font-bold tracking-tight text-white backdrop-blur-md">
                {formatNumber(timeLeft.hours)}
              </span>
              <span className="text-[9px] uppercase font-bold tracking-wider text-slate-500 mt-1">HRS</span>
            </div>
            <span className="text-xl font-extrabold text-slate-600 animate-pulse">:</span>
            <div className="flex flex-col items-center">
              <span className="rounded-lg bg-white/10 px-3 py-1 font-mono text-xl font-bold tracking-tight text-white backdrop-blur-md">
                {formatNumber(timeLeft.minutes)}
              </span>
              <span className="text-[9px] uppercase font-bold tracking-wider text-slate-500 mt-1">MIN</span>
            </div>
            <span className="text-xl font-extrabold text-slate-600 animate-pulse">:</span>
            <div className="flex flex-col items-center">
              <span className="rounded-lg bg-rose-500/20 px-3 py-1 font-mono text-xl font-bold tracking-tight text-rose-400 border border-rose-500/20">
                {formatNumber(timeLeft.seconds)}
              </span>
              <span className="text-[9px] uppercase font-bold tracking-wider text-slate-500 mt-1">SEC</span>
            </div>
          </div>
        </div>
      </div>

      {/* Security & System Check logs */}
      <div className="mt-5 rounded-xl bg-indigo-50/50 p-4 border border-indigo-100 text-xs text-indigo-950">
        <div className="flex items-start gap-3">
          <Cpu className="h-5 w-5 text-indigo-600 shrink-0 mt-0.5" />
          <div className="space-y-1">
            <p className="font-bold text-slate-900">Secure Online Proctoring Enabled</p>
            <p className="text-slate-600 leading-relaxed">
              By pressing <strong className="text-indigo-600 font-bold">Start Academic Exam</strong>, you authorize full screen lockdown, window change detection, and automated AI head-pose verification. Ensure a quiet room.
            </p>
          </div>
        </div>
      </div>

      {/* CTA Button */}
      <button
        onClick={onStartExam}
        className="group mt-6 flex w-full items-center justify-center gap-2.5 rounded-xl bg-indigo-600 py-4 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 transition-all hover:bg-indigo-700 hover:shadow-xl hover:shadow-indigo-600/35 active:scale-[0.98]"
      >
        <Play className="h-4 w-4 fill-current text-white group-hover:scale-110 transition-transform" />
        <span>Start Academic Exam</span>
      </button>
    </div>
  );
}

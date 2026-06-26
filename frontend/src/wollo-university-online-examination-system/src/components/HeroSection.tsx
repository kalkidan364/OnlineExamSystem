import React from 'react';
import { Award, BookOpen, GraduationCap, School, MapPin } from 'lucide-react';
import { StudentProfile } from '../types';

interface HeroSectionProps {
  profile: StudentProfile;
}

export default function HeroSection({ profile }: HeroSectionProps) {
  return (
    <div className="relative overflow-hidden rounded-2xl border border-indigo-100 bg-linear-to-r from-indigo-900 via-indigo-950 to-slate-950 p-6 text-white shadow-xl md:p-8">
      {/* Absolute Decorative Vector elements for premium layout */}
      <div className="absolute top-0 right-0 -translate-y-12 translate-x-12 opacity-10">
        <GraduationCap className="h-64 w-64" />
      </div>
      <div className="absolute -bottom-8 -left-8 h-48 w-48 rounded-full bg-indigo-600/10 blur-3xl"></div>
      <div className="absolute -top-8 right-1/4 h-32 w-32 rounded-full bg-purple-500/10 blur-2xl"></div>

      {/* Main Grid Content */}
      <div className="relative flex flex-col justify-between gap-6 md:flex-row md:items-center">
        {/* Student Welcome / Details */}
        <div className="space-y-4">
          <div className="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold backdrop-blur-md">
            <School className="h-3.5 w-3.5 text-indigo-400" />
            <span className="tracking-wide">Wollo University • Dessie Campus</span>
          </div>

          <div className="space-y-1.5">
            <h1 className="text-3xl font-bold tracking-tight md:text-4xl">
              Welcome back, {profile.name.split(' ')[0]} 👋
            </h1>
            <p className="text-sm font-medium text-slate-300">
              {profile.program} in <span className="text-white font-semibold">{profile.department}</span>
            </p>
          </div>

          <div className="flex flex-wrap gap-x-6 gap-y-2 text-xs text-slate-400">
            <div className="flex items-center gap-1.5">
              <span className="font-semibold text-slate-300">Student ID:</span>
              <span className="font-mono text-slate-100 bg-white/5 px-1.5 py-0.5 rounded">{profile.id}</span>
            </div>
            <div className="flex items-center gap-1.5">
              <span className="font-semibold text-slate-300">Dean List Rank:</span>
              <span className="text-indigo-400 font-bold">Top 2%</span>
            </div>
            <div className="flex items-center gap-1.5">
              <MapPin className="h-3 w-3 text-indigo-400" />
              <span>KIOT Computing block</span>
            </div>
          </div>
        </div>

        {/* Small Elegant KPI Board within Hero */}
        <div className="grid grid-cols-2 gap-3 min-w-[280px]">
          <div className="rounded-xl bg-white/5 p-4 border border-white/10 backdrop-blur-xs hover:bg-white/10 transition-colors">
            <div className="flex items-center gap-2 text-slate-300 mb-1">
              <Award className="h-4 w-4 text-amber-400" />
              <span className="text-xs font-medium uppercase tracking-wider">Current CGPA</span>
            </div>
            <p className="text-2xl font-bold font-mono tracking-tight text-white">{profile.cgpa.toFixed(2)}</p>
            <p className="text-[10px] text-emerald-400 font-semibold mt-0.5">First Class Honors</p>
          </div>

          <div className="rounded-xl bg-white/5 p-4 border border-white/10 backdrop-blur-xs hover:bg-white/10 transition-colors">
            <div className="flex items-center gap-2 text-slate-300 mb-1">
              <BookOpen className="h-4 w-4 text-indigo-400" />
              <span className="text-xs font-medium uppercase tracking-wider">Credits Done</span>
            </div>
            <p className="text-2xl font-bold font-mono tracking-tight text-white">{profile.creditsCompleted}</p>
            <p className="text-[10px] text-slate-400 mt-0.5">140 required for B.Sc.</p>
          </div>
        </div>
      </div>
    </div>
  );
}

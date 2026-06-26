import React, { useState, useEffect } from 'react';
import { Bell, LogOut, ShieldCheck, Clock, BookOpen, User } from 'lucide-react';
import { StudentProfile, Announcement } from '../types';

interface HeaderProps {
  profile: StudentProfile;
  announcements: Announcement[];
  onOpenProfile: () => void;
  onOpenNotifications: () => void;
}

export default function Header({ profile, announcements, onOpenProfile, onOpenNotifications }: HeaderProps) {
  const [time, setTime] = useState<string>('');

  useEffect(() => {
    const updateTime = () => {
      const now = new Date();
      setTime(now.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
      }));
    };
    updateTime();
    const interval = setInterval(updateTime, 1000);
    return () => clearInterval(interval);
  }, []);

  return (
    <header className="sticky top-0 z-40 w-full border-b border-slate-200 bg-white/85 backdrop-blur-md">
      <div className="mx-auto flex h-16 max-w-7xl items-center justify-between px-6">
        {/* Brand/Logo Section */}
        <div className="flex items-center gap-3">
          <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-md shadow-indigo-600/10">
            <span className="font-serif text-lg font-bold tracking-tight">W</span>
          </div>
          <div>
            <div className="flex items-center gap-2">
              <span className="font-serif text-base font-bold tracking-tight text-slate-900">WOLLO UNIVERSITY</span>
              <span className="rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-semibold text-indigo-600 uppercase tracking-wider">Senate Certified</span>
            </div>
            <p className="text-xs font-medium text-slate-500">Online Examination System</p>
          </div>
        </div>

        {/* Action Items / Statuses */}
        <div className="flex items-center gap-6">
          {/* Live System Time */}
          <div className="hidden items-center gap-2 rounded-lg bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-600 md:flex border border-slate-100">
            <Clock className="h-3.5 w-3.5 text-indigo-500 animate-pulse" />
            <span className="font-mono text-slate-700">{time || '04:28:41 AM'}</span>
          </div>

          {/* Academic Term Badge */}
          <span className="hidden rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700 sm:inline-block border border-slate-200">
            {profile.semester} • AY {profile.academicYear.split(' ')[0]}
          </span>

          {/* Alerts / Notifications Trigger */}
          <button
            onClick={onOpenNotifications}
            className="relative rounded-xl p-2 text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-800"
            aria-label="View Announcements"
          >
            <Bell className="h-5 w-5" />
            <span className="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-amber-500 ring-2 ring-white"></span>
          </button>

          {/* Student Profile Pill */}
          <div className="flex items-center gap-3 border-l border-slate-200 pl-6">
            <button
              onClick={onOpenProfile}
              className="flex items-center gap-3 text-left focus:outline-none group"
            >
              <div className="relative">
                <img
                  src={profile.avatar}
                  alt={profile.name}
                  className="h-9 w-9 rounded-full object-cover ring-2 ring-indigo-500/20 transition-all group-hover:ring-indigo-500"
                  referrerPolicy="no-referrer"
                />
                <span className="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full bg-emerald-500 ring-2 ring-white" title="Connected to Secure Exam Server"></span>
              </div>
              <div className="hidden lg:block">
                <p className="text-xs font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">{profile.name}</p>
                <p className="text-[10px] font-mono text-slate-500 uppercase tracking-wider">{profile.id}</p>
              </div>
            </button>
          </div>

          {/* Security Indicator & Logout */}
          <div className="flex items-center gap-2">
            <div className="hidden xl:flex items-center gap-1.5 text-[10px] uppercase tracking-wider font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md border border-emerald-100">
              <ShieldCheck className="h-3.5 w-3.5" />
              <span>Verified IP</span>
            </div>
            <button
              onClick={() => alert("To log out or switch student sessions, use your general dashboard settings in Google AI Studio.")}
              className="rounded-xl p-2 text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-all"
              title="Logout from Exam Portal"
            >
              <LogOut className="h-4 w-4" />
            </button>
          </div>
        </div>
      </div>
    </header>
  );
}

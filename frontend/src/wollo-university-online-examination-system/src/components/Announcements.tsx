import React, { useState } from 'react';
import { Megaphone, Calendar, Info, AlertTriangle, ArrowUpRight, X, User } from 'lucide-react';
import { Announcement } from '../types';

interface AnnouncementsProps {
  announcements: Announcement[];
}

export default function Announcements({ announcements }: AnnouncementsProps) {
  const [selectedAnn, setSelectedAnn] = useState<Announcement | null>(null);

  const getCategoryStyles = (category: string) => {
    switch (category) {
      case 'Urgent':
        return {
          bg: 'bg-rose-50 text-rose-700 border-rose-200',
          dot: 'bg-rose-500',
          iconColor: 'text-rose-500'
        };
      case 'Schedule':
        return {
          bg: 'bg-indigo-50 text-indigo-700 border-indigo-200',
          dot: 'bg-indigo-500',
          iconColor: 'text-indigo-500'
        };
      case 'System':
        return {
          bg: 'bg-slate-50 text-slate-700 border-slate-200',
          dot: 'bg-slate-500',
          iconColor: 'text-slate-500'
        };
      default:
        return {
          bg: 'bg-amber-50 text-amber-700 border-amber-200',
          dot: 'bg-amber-500',
          iconColor: 'text-amber-500'
        };
    }
  };

  return (
    <div className="rounded-xl border border-slate-200 bg-white p-6 shadow-xs h-full flex flex-col justify-between">
      <div>
        <div className="flex items-center justify-between mb-5">
          <div>
            <h3 className="text-lg font-bold text-slate-900">Official Notices</h3>
            <p className="text-xs text-slate-500 font-medium">Senate and ICT Department Announcements</p>
          </div>
          <Megaphone className="h-5 w-5 text-indigo-600 animate-bounce" />
        </div>

        <div className="space-y-3">
          {announcements.map((ann) => {
            const styles = getCategoryStyles(ann.category);
            return (
              <div
                key={ann.id}
                onClick={() => setSelectedAnn(ann)}
                className="group flex flex-col justify-between gap-2 rounded-xl border border-slate-100 bg-slate-50/50 p-4 transition-all hover:bg-slate-50 hover:border-slate-300 cursor-pointer"
              >
                <div className="flex items-center justify-between">
                  <span className={`inline-flex items-center gap-1.5 rounded-full border px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider ${styles.bg}`}>
                    <span className={`h-1.5 w-1.5 rounded-full ${styles.dot}`}></span>
                    {ann.category}
                  </span>
                  <span className="text-[10px] text-slate-400 font-medium">{ann.date}</span>
                </div>

                <div className="mt-1">
                  <h4 className="text-xs font-bold text-slate-800 group-hover:text-indigo-600 transition-colors flex items-center gap-1">
                    <span>{ann.title}</span>
                    <ArrowUpRight className="h-3.5 w-3.5 text-slate-300 group-hover:text-indigo-500 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all" />
                  </h4>
                  <p className="mt-1 text-xs text-slate-500 line-clamp-2 leading-relaxed">
                    {ann.content}
                  </p>
                </div>
              </div>
            );
          })}
        </div>
      </div>

      <div className="mt-5 text-center">
        <p className="text-[11px] font-semibold text-slate-400">All student alerts comply with Wollo Uni Senate regulations</p>
      </div>

      {/* Announcement Detail Modal */}
      {selectedAnn && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs">
          <div className="relative w-full max-w-lg rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl animate-in fade-in zoom-in-95 duration-150">
            <button
              onClick={() => setSelectedAnn(null)}
              className="absolute top-4 right-4 rounded-xl p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors"
            >
              <X className="h-5 w-5" />
            </button>

            <div className="flex items-center gap-2 mb-3">
              <span className={`inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-bold uppercase tracking-wider ${getCategoryStyles(selectedAnn.category).bg}`}>
                <span className={`h-1.5 w-1.5 rounded-full ${getCategoryStyles(selectedAnn.category).dot}`}></span>
                {selectedAnn.category}
              </span>
              <span className="text-xs text-slate-400 font-medium">{selectedAnn.date}</span>
            </div>

            <h3 className="text-lg font-black text-slate-900 pr-8">{selectedAnn.title}</h3>
            
            <p className="mt-4 text-xs font-mono text-indigo-600 flex items-center gap-1">
              <User className="h-3.5 w-3.5" />
              <span>Issued by: {selectedAnn.sender}</span>
            </p>

            <hr className="my-4 border-slate-100" />

            <div className="rounded-xl bg-slate-50 p-4 border border-slate-100">
              <p className="text-xs text-slate-600 leading-relaxed whitespace-pre-wrap font-sans">
                {selectedAnn.content}
              </p>
            </div>

            <button
              onClick={() => setSelectedAnn(null)}
              className="mt-6 w-full rounded-xl bg-slate-900 py-2.5 text-xs font-bold text-white hover:bg-slate-800 transition-colors"
            >
              Close Announcement
            </button>
          </div>
        </div>
      )}
    </div>
  );
}

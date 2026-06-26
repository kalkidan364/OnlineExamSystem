import React from 'react';
import { PenTool, BarChart3, FileBadge2, UserCog, CalendarDays, KeyRound } from 'lucide-react';

interface QuickActionsProps {
  onAction: (actionKey: 'take-exam' | 'view-results' | 'download-results' | 'update-profile' | 'academic-calendar') => void;
}

export default function QuickActions({ onAction }: QuickActionsProps) {
  const actions = [
    {
      key: 'take-exam' as const,
      title: 'Take Exam',
      description: 'Unlock and begin your current active paper with live AI proctoring',
      icon: PenTool,
      color: 'bg-indigo-50 text-indigo-700 border-indigo-100 hover:border-indigo-300 hover:bg-indigo-100/50'
    },
    {
      key: 'view-results' as const,
      title: 'View Results',
      description: 'Read graded papers, points breakdown, and direct instructor feedback',
      icon: BarChart3,
      color: 'bg-emerald-50 text-emerald-700 border-emerald-100 hover:border-emerald-300 hover:bg-emerald-100/50'
    },
    {
      key: 'download-results' as const,
      title: 'Print Transcript',
      description: 'Generate an official-looking Wollo University printable result slip',
      icon: FileBadge2,
      color: 'bg-blue-50 text-blue-700 border-blue-100 hover:border-blue-300 hover:bg-blue-100/50'
    },
    {
      key: 'update-profile' as const,
      title: 'Update Profile',
      description: 'Review your registered software engineering code and metadata details',
      icon: UserCog,
      color: 'bg-amber-50 text-amber-700 border-amber-100 hover:border-amber-300 hover:bg-amber-100/50'
    },
    {
      key: 'academic-calendar' as const,
      title: 'Academic Calendar',
      description: 'Check dates, lecture schedules, national holidays, and deadlines',
      icon: CalendarDays,
      color: 'bg-purple-50 text-purple-700 border-purple-100 hover:border-purple-300 hover:bg-purple-100/50'
    }
  ];

  return (
    <div className="space-y-4">
      <div>
        <h3 className="text-lg font-bold text-slate-900">Student Quick Actions</h3>
        <p className="text-xs text-slate-500 font-medium">Quick shortcuts to vital examination operations</p>
      </div>

      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        {actions.map((act) => {
          const IconComponent = act.icon;
          return (
            <button
              key={act.key}
              onClick={() => onAction(act.key)}
              className={`flex flex-col items-start text-left p-5 rounded-xl border transition-all duration-200 active:scale-[0.98] ${act.color}`}
            >
              <div className="rounded-lg bg-white p-2.5 shadow-xs border border-inherit mb-4">
                <IconComponent className="h-5 w-5" />
              </div>
              <h4 className="text-sm font-bold text-slate-800">{act.title}</h4>
              <p className="mt-1.5 text-xs text-slate-500 leading-relaxed font-sans line-clamp-2">
                {act.description}
              </p>
            </button>
          );
        })}
      </div>
    </div>
  );
}

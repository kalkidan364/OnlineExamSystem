import React, { useState } from 'react';
import { Calendar as CalendarIcon, Info, HelpCircle, ChevronLeft, ChevronRight } from 'lucide-react';
import { CalendarEvent } from '../types';

interface CalendarSectionProps {
  events: CalendarEvent[];
}

export default function CalendarSection({ events }: CalendarSectionProps) {
  const [selectedEvent, setSelectedEvent] = useState<CalendarEvent | null>(events[0]);

  // Visual simulation calendar days for June 2026
  // June 2026 starts on Monday, 1st. Has 30 days.
  const daysInJune = Array.from({ length: 30 }, (_, i) => i + 1);

  // Helper to find if a day has an event
  const getEventForDay = (day: number) => {
    const formattedDate = `2026-06-${day.toString().padStart(2, '0')}`;
    return events.find(e => e.date === formattedDate);
  };

  const getDayBadgeClass = (event: CalendarEvent | undefined) => {
    if (!event) return '';
    switch (event.type) {
      case 'Exam':
        return 'bg-indigo-600 text-white font-bold ring-2 ring-indigo-500/20';
      case 'Deadline':
        return 'bg-rose-500 text-white font-bold ring-2 ring-rose-500/20';
      case 'Holiday':
        return 'bg-slate-400 text-white font-bold ring-2 ring-slate-300/20';
      default:
        return 'bg-indigo-500 text-white';
    }
  };

  return (
    <div className="rounded-xl border border-slate-200 bg-white p-6 shadow-xs">
      <div className="flex items-center justify-between mb-5">
        <div>
          <h3 className="text-lg font-bold text-slate-900">Academic Calendar</h3>
          <p className="text-xs text-slate-500 font-medium">Critical dates and deadlines for this semester</p>
        </div>
        <div className="flex items-center gap-1">
          <CalendarIcon className="h-5 w-5 text-indigo-600" />
        </div>
      </div>

      <div className="grid gap-6 md:grid-cols-2">
        {/* Visual Monthly Calendar Grid */}
        <div className="border border-slate-100 rounded-xl p-4 bg-slate-50/50">
          <div className="flex items-center justify-between mb-4">
            <span className="text-xs font-extrabold uppercase tracking-widest text-indigo-600 font-sans">
              June 2026
            </span>
            <div className="flex items-center gap-1.5 text-slate-400">
              <ChevronLeft className="h-4 w-4 cursor-pointer hover:text-slate-600" />
              <ChevronRight className="h-4 w-4 cursor-pointer hover:text-slate-600" />
            </div>
          </div>

          {/* Days of Week */}
          <div className="grid grid-cols-7 text-center gap-y-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">
            <span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span><span>Su</span>
          </div>

          {/* June 2026 starts on Mon, so no leading blank days! */}
          <div className="grid grid-cols-7 text-center gap-1 text-xs">
            {daysInJune.map((day) => {
              const event = getEventForDay(day);
              const isSelected = selectedEvent && selectedEvent.date === `2026-06-${day.toString().padStart(2, '0')}`;
              
              return (
                <button
                  key={day}
                  onClick={() => event && setSelectedEvent(event)}
                  className={`flex h-8 w-full items-center justify-center rounded-lg transition-all focus:outline-none ${
                    event 
                      ? `${getDayBadgeClass(event)} cursor-pointer scale-105 shadow-xs` 
                      : 'text-slate-600 hover:bg-slate-100'
                  } ${isSelected ? 'ring-2 ring-indigo-500 ring-offset-2' : ''} ${day === 26 ? 'font-black underline' : ''}`}
                  title={event ? `${event.title} (${event.type})` : `June ${day}`}
                >
                  {day}
                </button>
              );
            })}
          </div>

          {/* Legend */}
          <div className="mt-4 flex flex-wrap justify-center gap-3 text-[10px] font-semibold text-slate-500 border-t border-slate-100 pt-3">
            <div className="flex items-center gap-1">
              <span className="h-2 w-2 rounded-full bg-indigo-600"></span>
              <span>Exam</span>
            </div>
            <div className="flex items-center gap-1">
              <span className="h-2 w-2 rounded-full bg-rose-500"></span>
              <span>Deadline</span>
            </div>
            <div className="flex items-center gap-1">
              <span className="h-2 w-2 rounded-full bg-slate-400"></span>
              <span>Holiday</span>
            </div>
          </div>
        </div>

        {/* Selected Date Information details */}
        <div className="flex flex-col justify-between p-2">
          {selectedEvent ? (
            <div className="space-y-4">
              <div className="inline-flex items-center gap-2 rounded-md bg-indigo-50 px-2 py-1 text-[10px] font-bold text-indigo-700 border border-indigo-100 uppercase tracking-widest">
                {selectedEvent.type} Schedule
              </div>
              <div>
                <h4 className="text-base font-bold text-slate-900">{selectedEvent.title}</h4>
                <p className="text-xs font-medium text-indigo-500 mt-1">Date: {selectedEvent.date}</p>
              </div>
              <p className="text-xs text-slate-600 leading-relaxed font-sans bg-slate-50 p-3 rounded-lg border border-slate-100">
                {selectedEvent.description}
              </p>
            </div>
          ) : (
            <div className="flex h-full flex-col items-center justify-center text-center p-6 text-slate-400 border border-dashed border-slate-200 rounded-xl">
              <Info className="h-8 w-8 text-slate-300 mb-2" />
              <p className="text-xs font-medium">Select a colored calendar date to view the active event details.</p>
            </div>
          )}

          {/* Prompt warning */}
          <div className="rounded-lg bg-amber-50 p-3 border border-amber-100 text-[11px] text-amber-800 font-medium mt-4">
            Verify all deadlines with your academic coordinator. Schedule is subject to change based on university senate updates.
          </div>
        </div>
      </div>
    </div>
  );
}

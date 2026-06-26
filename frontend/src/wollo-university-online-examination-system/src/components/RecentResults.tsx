import React from 'react';
import { FileSpreadsheet, Eye, ChevronRight, FileDown } from 'lucide-react';
import { RecentResult } from '../types';

interface RecentResultsProps {
  results: RecentResult[];
  onViewResult: (result: RecentResult) => void;
  onDownloadTranscript: () => void;
}

export default function RecentResults({ results, onViewResult, onDownloadTranscript }: RecentResultsProps) {
  return (
    <div className="rounded-xl border border-slate-200 bg-white p-6 shadow-xs">
      {/* Table Title and Actions */}
      <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
          <h3 className="text-lg font-bold text-slate-900">Recent Term Results</h3>
          <p className="text-xs text-slate-500 font-medium">Verified evaluations by the Department of Software Engineering</p>
        </div>
        <button
          onClick={onDownloadTranscript}
          className="inline-flex items-center gap-2 rounded-lg border border-indigo-200 bg-indigo-50 px-3.5 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-100 transition-colors active:scale-[0.99]"
        >
          <FileDown className="h-4 w-4" />
          <span>View Transcript Slip</span>
        </button>
      </div>

      {/* Responsive Table Container */}
      <div className="overflow-x-auto">
        <table className="w-full text-left border-collapse">
          <thead>
            <tr className="border-b border-slate-100 text-xs font-bold uppercase tracking-wider text-slate-400">
              <th className="py-3 px-4 pl-0">Course Code & Name</th>
              <th className="py-3 px-4">Evaluation Mode</th>
              <th className="py-3 px-4 text-center">Score Ratio</th>
              <th className="py-3 px-4 text-center">Grade</th>
              <th className="py-3 px-4 text-center">Status</th>
              <th className="py-3 px-4 text-right pr-0">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-100">
            {results.map((result) => (
              <tr key={result.id} className="group hover:bg-slate-50/70 transition-colors">
                {/* Course Code & Name */}
                <td className="py-4 px-4 pl-0 max-w-[280px]">
                  <p className="text-xs font-mono font-bold text-indigo-600 uppercase tracking-wide">
                    {result.courseCode}
                  </p>
                  <p className="font-bold text-slate-800 text-sm truncate mt-0.5" title={result.courseName}>
                    {result.courseName}
                  </p>
                  <p className="text-[10px] text-slate-400 font-medium mt-0.5">Completed {result.completedDate}</p>
                </td>

                {/* Evaluation Mode */}
                <td className="py-4 px-4 text-xs font-semibold text-slate-600">
                  {result.examTitle}
                </td>

                {/* Score Ratio */}
                <td className="py-4 px-4 text-center">
                  <span className="font-mono text-sm font-bold text-slate-800">
                    {result.score}
                  </span>
                  <span className="font-mono text-xs text-slate-400">/{result.totalMarks}</span>
                  <span className="block text-[10px] font-semibold text-indigo-500 font-mono mt-0.5">
                    {result.percentage}%
                  </span>
                </td>

                {/* Grade Badge */}
                <td className="py-4 px-4 text-center">
                  <span className="inline-block rounded-md bg-indigo-50 px-2.5 py-1 font-mono text-xs font-extrabold text-indigo-700 border border-indigo-100">
                    {result.grade}
                  </span>
                </td>

                {/* Status Badge */}
                <td className="py-4 px-4 text-center">
                  <span className="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 border border-emerald-100">
                    <span className="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    Passed
                  </span>
                </td>

                {/* Actions Trigger */}
                <td className="py-4 px-4 text-right pr-0">
                  <button
                    onClick={() => onViewResult(result)}
                    className="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-600 hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900 transition-colors"
                  >
                    <Eye className="h-3.5 w-3.5" />
                    <span>Review Paper</span>
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

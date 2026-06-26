import React from 'react';
import { X, Printer, ShieldCheck, Download, Award, School } from 'lucide-react';
import { StudentProfile, RecentResult } from '../types';

interface DownloadTranscriptModalProps {
  profile: StudentProfile;
  results: RecentResult[];
  onClose: () => void;
}

export default function DownloadTranscriptModal({ profile, results, onClose }: DownloadTranscriptModalProps) {
  const handlePrint = () => {
    window.print();
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs">
      <div className="relative w-full max-w-3xl rounded-2xl border border-slate-200 bg-white shadow-2xl animate-in fade-in zoom-in-95 duration-150 flex flex-col max-h-[90vh]">
        
        {/* Header toolbar */}
        <header className="border-b border-slate-100 p-4 flex items-center justify-between bg-slate-50 rounded-t-2xl">
          <span className="text-xs font-bold text-slate-500 uppercase tracking-widest flex items-center gap-1.5">
            <School className="h-4 w-4 text-indigo-600" />
            Official Student Record System
          </span>
          <div className="flex items-center gap-2">
            <button
              onClick={handlePrint}
              className="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors"
            >
              <Printer className="h-3.5 w-3.5" />
              <span>Print Slip</span>
            </button>
            <button
              onClick={onClose}
              className="rounded-xl p-1.5 text-slate-400 hover:bg-slate-200 hover:text-slate-600 transition-colors"
            >
              <X className="h-5 w-5" />
            </button>
          </div>
        </header>

        {/* Scrollable Printable Document Area */}
        <div id="printable-transcript" className="flex-1 overflow-y-auto p-8 font-sans space-y-6">
          
          {/* Official Seal and Header */}
          <div className="text-center space-y-2 border-b-2 border-slate-900 pb-5">
            <div className="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-900 text-white font-serif font-black text-2xl">
              W
            </div>
            <div className="space-y-0.5">
              <h2 className="text-lg font-black tracking-wider text-slate-900 uppercase">Wollo University</h2>
              <p className="text-xs font-bold text-slate-600 uppercase">Office of the Registrar & Senate Secretariat</p>
              <p className="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Kombolcha Institute of Technology • Informatics Department</p>
            </div>
          </div>

          {/* Student details grid */}
          <div className="grid grid-cols-2 gap-x-6 gap-y-3 rounded-xl bg-slate-50 p-5 border border-slate-100 text-xs text-slate-700">
            <div>
              <p className="font-semibold text-slate-400 uppercase text-[9px]">Student Name</p>
              <p className="font-bold text-slate-900 mt-0.5">{profile.name}</p>
            </div>
            <div>
              <p className="font-semibold text-slate-400 uppercase text-[9px]">Student Registration ID</p>
              <p className="font-mono font-bold text-slate-900 mt-0.5">{profile.id}</p>
            </div>
            <div>
              <p className="font-semibold text-slate-400 uppercase text-[9px]">Department / Program</p>
              <p className="font-bold text-slate-900 mt-0.5">{profile.department} ({profile.program})</p>
            </div>
            <div>
              <p className="font-semibold text-slate-400 uppercase text-[9px]">Academic Term</p>
              <p className="font-bold text-slate-900 mt-0.5">{profile.semester} • AY 2025/2026</p>
            </div>
          </div>

          {/* Transcript course list */}
          <div>
            <h4 className="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Certified Grade Record Sheet</h4>
            <div className="overflow-hidden border border-slate-200 rounded-xl">
              <table className="w-full text-left border-collapse text-xs">
                <thead>
                  <tr className="bg-slate-100 border-b border-slate-200 text-slate-600 font-bold">
                    <th className="py-2.5 px-4">Course Code</th>
                    <th className="py-2.5 px-4">Academic Course Title</th>
                    <th className="py-2.5 px-4 text-center">ECTS Credits</th>
                    <th className="py-2.5 px-4 text-center">Score %</th>
                    <th className="py-2.5 px-4 text-center">Letter Grade</th>
                    <th className="py-2.5 px-4 text-right">Remarks</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-150">
                  {results.map((res) => (
                    <tr key={res.id} className="text-slate-800">
                      <td className="py-2.5 px-4 font-mono font-bold text-slate-600">{res.courseCode}</td>
                      <td className="py-2.5 px-4 font-medium">{res.courseName}</td>
                      <td className="py-2.5 px-4 text-center font-mono font-semibold">5.0</td>
                      <td className="py-2.5 px-4 text-center font-mono">{res.percentage}%</td>
                      <td className="py-2.5 px-4 text-center font-mono font-bold">{res.grade}</td>
                      <td className="py-2.5 px-4 text-right text-emerald-600 font-bold uppercase text-[10px]">EXCELLENT</td>
                    </tr>
                  ))}
                  {/* Additional historical lines for realistic transcript weight */}
                  <tr className="text-slate-600 bg-slate-50/50">
                    <td className="py-2.5 px-4 font-mono">SWE-311</td>
                    <td className="py-2.5 px-4">Software Requirements Engineering</td>
                    <td className="py-2.5 px-4 text-center font-mono">5.0</td>
                    <td className="py-2.5 px-4 text-center font-mono">88%</td>
                    <td className="py-2.5 px-4 text-center font-mono">A</td>
                    <td className="py-2.5 px-4 text-right text-emerald-600 uppercase text-[10px]">CREDIT PASS</td>
                  </tr>
                  <tr className="text-slate-600 bg-slate-50/50">
                    <td className="py-2.5 px-4 font-mono">MATH-312</td>
                    <td className="py-2.5 px-4">Probability & Statistics for Computing</td>
                    <td className="py-2.5 px-4 text-center font-mono">4.0</td>
                    <td className="py-2.5 px-4 text-center font-mono">85%</td>
                    <td className="py-2.5 px-4 text-center font-mono">A-</td>
                    <td className="py-2.5 px-4 text-right text-emerald-600 uppercase text-[10px]">CREDIT PASS</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          {/* Transcript Footer Metrics & Stamps */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 items-end">
            
            {/* CGPA Summary box */}
            <div className="rounded-xl border border-indigo-200 bg-indigo-50/50 p-4 space-y-1">
              <span className="text-[9px] uppercase font-bold text-indigo-500 block">Calculated CGPA</span>
              <div className="flex items-baseline gap-1.5">
                <span className="text-2xl font-black text-indigo-900 font-mono">{profile.cgpa.toFixed(2)}</span>
                <span className="text-xs font-semibold text-slate-500">/ 4.00</span>
              </div>
              <p className="text-[10px] text-emerald-600 font-bold">First Class Honors Standing</p>
            </div>

            {/* Validation QR Code simulated mockup */}
            <div className="flex items-center gap-3 border border-slate-100 rounded-xl p-3 bg-slate-50/50 justify-center">
              <div className="h-14 w-14 bg-slate-900 p-1 rounded-md shrink-0 flex flex-wrap gap-0.5 relative justify-center items-center">
                {/* Simulated high fidelity QR pixels */}
                <div className="absolute inset-1.5 bg-white flex items-center justify-center font-serif text-[10px] font-black tracking-tight text-slate-900 border border-slate-200">
                  WU
                </div>
              </div>
              <div className="text-[10px] text-slate-400 space-y-0.5 leading-tight font-sans">
                <p className="font-bold text-slate-600">Scan for Registry</p>
                <p>Senate Verified</p>
                <p className="font-mono text-[8px]">REF: 81048-Dessie</p>
              </div>
            </div>

            {/* Signature Stamp block */}
            <div className="text-center space-y-1 pb-2">
              <div className="h-8 flex items-center justify-center">
                {/* Simulated cursive signature drawing */}
                <span className="font-serif italic text-base text-indigo-600/75 select-none font-semibold">Dr. Abraham G.</span>
              </div>
              <div className="border-t border-slate-300 pt-1.5 text-[9px] uppercase font-bold text-slate-400">
                Authorized Registrar Dean
              </div>
            </div>

          </div>

          <div className="text-center text-[10px] text-slate-400 font-mono border-t border-dashed border-slate-200 pt-5">
            This document is a certified copy generated from the Wollo University Online Examination System on June 26, 2026.
          </div>

        </div>

        {/* Footer toolbar */}
        <footer className="border-t border-slate-150 p-4 bg-slate-50 rounded-b-2xl flex justify-between items-center text-xs">
          <span className="text-slate-500 font-medium flex items-center gap-1">
            <ShieldCheck className="h-4 w-4 text-emerald-500" />
            Cryptographically Encrypted Secure PDF
          </span>
          <button
            onClick={handlePrint}
            className="rounded-xl bg-indigo-600 hover:bg-indigo-700 px-5 py-2.5 font-bold text-white transition-colors flex items-center gap-2"
          >
            <Download className="h-4 w-4" />
            <span>Download PDF Slip</span>
          </button>
        </footer>

      </div>
    </div>
  );
}

import React, { useState } from 'react';
import { X, User, Mail, Award, BookOpen, UserCheck, ShieldCheck, HeartPulse } from 'lucide-react';
import { StudentProfile } from '../types';

interface ProfileModalProps {
  profile: StudentProfile;
  onClose: () => void;
  onUpdateProfile: (updated: StudentProfile) => void;
}

export default function ProfileModal({ profile, onClose, onUpdateProfile }: ProfileModalProps) {
  const [name, setName] = useState(profile.name);
  const [email, setEmail] = useState(profile.email);
  const [avatar, setAvatar] = useState(profile.avatar);
  const [isSaved, setIsSaved] = useState(false);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    onUpdateProfile({
      ...profile,
      name,
      email,
      avatar
    });
    setIsSaved(true);
    setTimeout(() => {
      setIsSaved(false);
      onClose();
    }, 1500);
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs">
      <div className="relative w-full max-w-lg rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl animate-in fade-in zoom-in-95 duration-150">
        <button
          onClick={onClose}
          className="absolute top-4 right-4 rounded-xl p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors"
        >
          <X className="h-5 w-5" />
        </button>

        <div className="flex items-center gap-3 mb-6">
          <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100">
            <User className="h-5 w-5" />
          </div>
          <div>
            <h3 className="text-base font-black text-slate-900">Registered Profile</h3>
            <p className="text-xs text-slate-500 font-medium">Verify or adjust your academic enrollment details</p>
          </div>
        </div>

        <form onSubmit={handleSubmit} className="space-y-4">
          
          {/* Avatar preview and input */}
          <div className="flex items-center gap-4 bg-slate-50 p-4 rounded-xl border border-slate-100 mb-4">
            <img
              src={avatar}
              alt="Avatar Preview"
              className="h-16 w-16 rounded-full object-cover ring-2 ring-indigo-500/20"
              referrerPolicy="no-referrer"
            />
            <div className="space-y-1 flex-1">
              <label className="text-[10px] uppercase font-bold text-slate-400 block">Avatar URL</label>
              <input
                type="text"
                value={avatar}
                onChange={(e) => setAvatar(e.target.value)}
                className="w-full text-xs font-mono text-slate-600 bg-white border border-slate-200 px-3 py-1.5 rounded-lg focus:border-indigo-500 focus:outline-hidden"
              />
            </div>
          </div>

          <div className="space-y-1.5">
            <label className="text-[10px] uppercase font-bold text-slate-400 block">Student Spelling Name</label>
            <input
              type="text"
              required
              value={name}
              onChange={(e) => setName(e.target.value)}
              className="w-full text-xs font-sans text-slate-800 bg-white border border-slate-200 px-3.5 py-2.5 rounded-xl focus:border-indigo-500 focus:outline-hidden"
            />
          </div>

          <div className="space-y-1.5">
            <label className="text-[10px] uppercase font-bold text-slate-400 block">Registered Email Address</label>
            <input
              type="email"
              required
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              className="w-full text-xs font-mono text-slate-800 bg-white border border-slate-200 px-3.5 py-2.5 rounded-xl focus:border-indigo-500 focus:outline-hidden"
            />
          </div>

          {/* Academic Meta fields (read only for security!) */}
          <div className="grid grid-cols-2 gap-3 pt-2">
            <div className="bg-slate-50/50 p-3 rounded-lg border border-slate-100">
              <span className="text-[9px] uppercase font-bold text-slate-400 block">Department code</span>
              <span className="text-xs font-bold text-slate-800 mt-0.5 block">{profile.department}</span>
            </div>
            <div className="bg-slate-50/50 p-3 rounded-lg border border-slate-100">
              <span className="text-[9px] uppercase font-bold text-slate-400 block">Academic ID Code</span>
              <span className="text-xs font-mono font-bold text-slate-800 mt-0.5 block">{profile.id}</span>
            </div>
          </div>

          <div className="rounded-lg bg-emerald-50 p-3 border border-emerald-100 text-[11px] text-emerald-800 flex items-start gap-2">
            <ShieldCheck className="h-4.5 w-4.5 text-emerald-600 shrink-0 mt-0.5" />
            <span>Profile verification complete. These details are synchronized securely with the Wollo University student directory.</span>
          </div>

          {/* Form CTAs */}
          <div className="pt-4 flex gap-3">
            <button
              type="button"
              onClick={onClose}
              className="flex-1 rounded-xl border border-slate-200 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors"
            >
              Discard Changes
            </button>
            <button
              type="submit"
              className="flex-1 rounded-xl bg-indigo-600 py-2.5 text-xs font-bold text-white hover:bg-indigo-700 transition-colors flex items-center justify-center gap-1.5"
            >
              {isSaved ? (
                <>
                  <UserCheck className="h-4 w-4" />
                  <span>Details Saved!</span>
                </>
              ) : (
                <span>Save Registry</span>
              )}
            </button>
          </div>

        </form>
      </div>
    </div>
  );
}

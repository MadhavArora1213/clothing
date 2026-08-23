import React from 'react';
import { CheckCircle2, AlertCircle, Info, X } from 'lucide-react';
import { useStore } from '../../context/StoreContext';

export const ToastContainer = () => {
  const { toasts, removeToast } = useStore();

  if (toasts.length === 0) return null;

  return (
    <div className="fixed bottom-6 right-6 z-50 flex flex-col gap-2.5 max-w-sm w-full pointer-events-none">
      {toasts.map((toast) => {
        const isSuccess = toast.type === 'success';
        const isError = toast.type === 'error';

        return (
          <div
            key={toast.id}
            className={`pointer-events-auto flex items-center justify-between gap-3 p-4 rounded-2xl shadow-xl border backdrop-blur-md transition-all duration-300 animate-slide-in-right ${
              isSuccess
                ? 'bg-white/95 border-emerald-200 text-stone-900 shadow-emerald-500/10'
                : isError
                  ? 'bg-white/95 border-rose-200 text-stone-900 shadow-rose-500/10'
                  : 'bg-white/95 border-stone-200 text-stone-900 shadow-stone-500/10'
            }`}
          >
            <div className="flex items-center gap-3 min-w-0">
              {isSuccess && <CheckCircle2 className="w-5 h-5 text-emerald-600 shrink-0" />}
              {isError && <AlertCircle className="w-5 h-5 text-rose-600 shrink-0" />}
              {!isSuccess && !isError && <Info className="w-5 h-5 text-sky-600 shrink-0" />}
              <p className="text-xs font-semibold leading-tight line-clamp-2">{toast.message}</p>
            </div>

            <button
              onClick={() => removeToast(toast.id)}
              className="text-stone-400 hover:text-stone-700 p-1 rounded-full hover:bg-stone-100 transition-colors shrink-0"
            >
              <X className="w-4 h-4" />
            </button>
          </div>
        );
      })}
    </div>
  );
};

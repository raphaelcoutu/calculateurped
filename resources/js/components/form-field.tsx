import type { ReactNode } from 'react';

export const inputClass = 'mt-1 block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-sm text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-white/15 dark:bg-[#0f1918] dark:text-white dark:placeholder:text-slate-500';

export function FormField({ id, label, error, children, hint }: {
    id: string;
    label: string;
    error?: string;
    hint?: string;
    children: ReactNode;
}) {
    return (
        <div className="space-y-1.5">
            <label htmlFor={id} className="block text-sm font-medium text-slate-700 dark:text-slate-200">{label}</label>
            {children}
            {hint && <p className="text-xs text-slate-500 dark:text-slate-400">{hint}</p>}
            {error && <p className="text-sm text-rose-700 dark:text-rose-300" role="alert">{error}</p>}
        </div>
    );
}

export function PrimaryButton({ children, disabled, type = 'submit' }: {
    children: ReactNode;
    disabled?: boolean;
    type?: 'button' | 'submit';
}) {
    return <button type={type} disabled={disabled} className="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-4 focus:ring-brand-500/20 disabled:cursor-wait disabled:opacity-60">{children}</button>;
}

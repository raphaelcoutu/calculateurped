import { Head, useForm } from '@inertiajs/react';
import AuthLayout from '@/components/auth-layout';
import { FormField, inputClass, PrimaryButton } from '@/components/form-field';
import type { FormEvent } from 'react';

export default function ResetPassword({ token, email }: { token: string; email: string }) {
    const form = useForm({ token, email, password: '', password_confirmation: '' });

    function submit(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();
        form.post('/reset-password');
    }

    return (
        <AuthLayout>
            <Head title="Choisir un mot de passe" />
            <section className="rounded-3xl border border-white/80 bg-white p-7 shadow-xl shadow-slate-900/5 dark:border-white/10 dark:bg-[#182524] sm:p-9">
                <p className="text-xs font-semibold uppercase tracking-[0.18em] text-brand-700 dark:text-brand-100">Sécurité du compte</p>
                <h1 className="mt-2 text-2xl font-semibold tracking-tight text-slate-950 dark:text-white">Choisir un mot de passe</h1>
                <p className="mb-6 mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">Utilisez au moins 8 caractères pour protéger votre compte.</p>
                <form onSubmit={submit} className="space-y-5">
                    <FormField id="email" label="Adresse courriel" error={form.errors.email}>
                        <input id="email" type="email" autoComplete="username" required value={form.data.email} onChange={event => form.setData('email', event.currentTarget.value)} className={inputClass} />
                    </FormField>
                    <FormField id="password" label="Nouveau mot de passe" error={form.errors.password}>
                        <input id="password" type="password" autoComplete="new-password" minLength={8} required value={form.data.password} onChange={event => form.setData('password', event.currentTarget.value)} className={inputClass} />
                    </FormField>
                    <FormField id="password_confirmation" label="Confirmer le mot de passe" error={form.errors.password_confirmation}>
                        <input id="password_confirmation" type="password" autoComplete="new-password" minLength={8} required value={form.data.password_confirmation} onChange={event => form.setData('password_confirmation', event.currentTarget.value)} className={inputClass} />
                    </FormField>
                    <PrimaryButton disabled={form.processing}>{form.processing ? 'Enregistrement…' : 'Enregistrer le mot de passe'}</PrimaryButton>
                </form>
            </section>
        </AuthLayout>
    );
}

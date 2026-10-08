import { Head, Link, useForm } from '@inertiajs/react';
import AuthLayout from '@/components/auth-layout';
import { FormField, inputClass, PrimaryButton } from '@/components/form-field';
import type { FormEvent } from 'react';

export default function Login() {
    const form = useForm({ email: '', password: '', remember: false });

    function submit(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();
        form.post('/login', {
            onError: () => form.setData('password', ''),
        });
    }

    return (
        <AuthLayout>
            <Head title="Connexion" />
            <section className="rounded-3xl border border-white/80 bg-white p-7 shadow-xl shadow-slate-900/5 dark:border-white/10 dark:bg-[#182524] sm:p-9">
                <div className="mb-7">
                    <p className="text-xs font-semibold uppercase tracking-[0.18em] text-brand-700 dark:text-brand-100">Connexion</p>
                    <h1 className="mt-2 text-2xl font-semibold tracking-tight text-slate-950 dark:text-white">Bienvenue dans votre espace</h1>
                    <p className="mt-2 text-sm text-slate-600 dark:text-slate-300">Connectez-vous avec l’adresse courriel associée à votre invitation.</p>
                </div>
                <form onSubmit={submit} className="space-y-5">
                    <FormField id="email" label="Adresse courriel" error={form.errors.email}>
                        <input id="email" type="email" autoComplete="username" autoFocus required value={form.data.email} onChange={event => form.setData('email', event.currentTarget.value)} className={inputClass} />
                    </FormField>
                    <FormField id="password" label="Mot de passe" error={form.errors.password}>
                        <input id="password" type="password" autoComplete="current-password" required value={form.data.password} onChange={event => form.setData('password', event.currentTarget.value)} className={inputClass} />
                    </FormField>
                    <label className="flex items-center gap-2.5 text-sm text-slate-600 dark:text-slate-300">
                        <input type="checkbox" checked={form.data.remember} onChange={event => form.setData('remember', event.currentTarget.checked)} className="size-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500" />
                        Garder ma session ouverte
                    </label>
                    <div className="flex flex-col gap-3 pt-1">
                        <PrimaryButton disabled={form.processing}>{form.processing ? 'Connexion…' : 'Se connecter'}</PrimaryButton>
                        <Link href="/forgot-password" className="text-center text-sm font-medium text-brand-700 underline-offset-4 hover:underline dark:text-brand-100">Mot de passe oublié?</Link>
                    </div>
                </form>
            </section>
        </AuthLayout>
    );
}

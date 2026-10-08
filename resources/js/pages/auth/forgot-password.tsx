import { Head, Link, useForm } from '@inertiajs/react';
import AuthLayout from '@/components/auth-layout';
import { FormField, inputClass, PrimaryButton } from '@/components/form-field';
import type { FormEvent } from 'react';

export default function ForgotPassword() {
    const form = useForm({ email: '' });

    function submit(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();
        form.post('/password/email');
    }

    return (
        <AuthLayout>
            <Head title="Définir ou réinitialiser le mot de passe" />
            <section className="rounded-3xl border border-white/80 bg-white p-7 shadow-xl shadow-slate-900/5 dark:border-white/10 dark:bg-[#182524] sm:p-9">
                <p className="text-xs font-semibold uppercase tracking-[0.18em] text-brand-700 dark:text-brand-100">Aide à la connexion</p>
                <h1 className="mt-2 text-2xl font-semibold tracking-tight text-slate-950 dark:text-white">Définir ou réinitialiser un mot de passe</h1>
                <p className="mb-6 mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">Saisissez l’adresse courriel de votre compte. Nous vous enverrons un lien sécurisé.</p>
                <form onSubmit={submit} className="space-y-5">
                    <FormField id="email" label="Adresse courriel" error={form.errors.email}>
                        <input id="email" type="email" autoComplete="username" autoFocus required value={form.data.email} onChange={event => form.setData('email', event.currentTarget.value)} className={inputClass} />
                    </FormField>
                    <PrimaryButton disabled={form.processing}>{form.processing ? 'Envoi…' : 'Envoyer le lien'}</PrimaryButton>
                </form>
                <Link href="/login" className="mt-5 inline-block text-sm font-medium text-brand-700 underline-offset-4 hover:underline dark:text-brand-100">Retour à la connexion</Link>
            </section>
        </AuthLayout>
    );
}

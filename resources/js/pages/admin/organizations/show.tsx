import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import AppLayout from '@/components/app-layout';
import { FormField, inputClass, PrimaryButton } from '@/components/form-field';

type Organization = { id: number; name: string; logoUrl: string | null; users: { id: number; name: string; email: string }[] };

export default function OrganizationShow({ organization }: { organization: Organization }) {
    const form = useForm({ name: '', email: '' });

    function invite(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();
        form.post(`/admin/organizations/${organization.id}/administrators`);
    }

    return (
        <AppLayout>
            <Head title={organization.name} />
            <div className="mb-8 flex flex-wrap items-start justify-between gap-4">
                <div>
                    <Link href="/admin/organizations" className="text-sm font-medium text-brand-700 hover:underline dark:text-brand-100">← Toutes les organisations</Link>
                    <p className="mt-5 text-xs font-semibold uppercase tracking-[0.18em] text-brand-700 dark:text-brand-100">Organisation hospitalière</p>
                    <h1 className="mt-2 text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">{organization.name}</h1>
                </div>
                <Link href={`/admin/organizations/${organization.id}/edit`} className="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-white/15 dark:bg-[#182524] dark:text-slate-200 dark:hover:bg-white/5">Modifier l’organisation</Link>
            </div>
            {organization.logoUrl && <div className="mb-8 rounded-2xl border border-slate-200 bg-white p-5 dark:border-white/10 dark:bg-[#182524]"><img src={organization.logoUrl} alt={`Logo de ${organization.name}`} className="max-h-24 max-w-48 object-contain" /></div>}
            <div className="grid gap-6 lg:grid-cols-[1fr_0.85fr]">
                <section className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#182524]">
                    <div className="border-b border-slate-100 px-5 py-4 dark:border-white/10">
                        <h2 className="font-semibold text-slate-900 dark:text-white">Administrateurs</h2>
                        <p className="mt-1 text-sm text-slate-500 dark:text-slate-400">{organization.users.length} compte{organization.users.length === 1 ? '' : 's'} associé{organization.users.length === 1 ? '' : 's'} à cette organisation</p>
                    </div>
                    {organization.users.length ? <ul className="divide-y divide-slate-100 dark:divide-white/10">{organization.users.map(user => <li key={user.id} className="flex items-center gap-3 px-5 py-4"><span className="grid size-10 place-items-center rounded-full bg-brand-50 font-semibold text-brand-700 dark:bg-brand-900/50 dark:text-brand-100">{user.name.slice(0, 1).toUpperCase()}</span><span><span className="block text-sm font-medium text-slate-900 dark:text-white">{user.name}</span><span className="block text-sm text-slate-500 dark:text-slate-400">{user.email}</span></span></li>)}</ul> : <p className="px-5 py-8 text-sm text-slate-500 dark:text-slate-400">Aucun compte administrateur n’a encore été invité.</p>}
                </section>
                <section className="h-fit rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#182524] sm:p-6">
                    <div className="mb-5"><p className="text-xs font-semibold uppercase tracking-[0.18em] text-brand-700 dark:text-brand-100">Nouvel accès</p><h2 className="mt-2 text-xl font-semibold text-slate-950 dark:text-white">Inviter un administrateur</h2><p className="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">Un lien sécurisé sera envoyé pour lui permettre de choisir son mot de passe.</p></div>
                    <form onSubmit={invite} className="space-y-4">
                        <FormField id="administrator_name" label="Nom" error={form.errors.name}><input id="administrator_name" maxLength={255} required value={form.data.name} onChange={event => form.setData('name', event.currentTarget.value)} className={inputClass} /></FormField>
                        <FormField id="administrator_email" label="Adresse courriel" error={form.errors.email}><input id="administrator_email" type="email" required value={form.data.email} onChange={event => form.setData('email', event.currentTarget.value)} className={inputClass} /></FormField>
                        <PrimaryButton disabled={form.processing}>{form.processing ? 'Envoi…' : 'Créer le compte et envoyer le lien'}</PrimaryButton>
                    </form>
                </section>
            </div>
        </AppLayout>
    );
}

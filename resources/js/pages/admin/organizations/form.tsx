import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import AppLayout from '@/components/app-layout';
import { FormField, inputClass, PrimaryButton } from '@/components/form-field';

type Organization = { id: number; name: string; logoUrl: string | null } | null;

export default function OrganizationForm({ organization, action, method }: {
    organization: Organization;
    action: string;
    method: 'post' | 'put';
}) {
    const form = useForm<{ name: string; logo: File | null }>({ name: organization?.name ?? '', logo: null });
    const isEditing = method === 'put';

    function submit(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();

        if (isEditing) {
            form.transform(data => ({ ...data, _method: 'put' }));
            form.post(action, { forceFormData: true });
            return;
        }

        form.post(action, { forceFormData: true });
    }

    return (
        <AppLayout>
            <Head title={isEditing ? 'Modifier une organisation' : 'Créer une organisation'} />
            <div className="mb-6">
                <Link href={organization ? `/admin/organizations/${organization.id}` : '/admin/organizations'} className="text-sm font-medium text-brand-700 hover:underline dark:text-brand-100">← Retour aux organisations</Link>
                <p className="mt-5 text-xs font-semibold uppercase tracking-[0.18em] text-brand-700 dark:text-brand-100">Administration</p>
                <h1 className="mt-2 text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">{isEditing ? `Modifier ${organization?.name}` : 'Créer une organisation'}</h1>
            </div>
            <section className="max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#182524] sm:p-8">
                <form onSubmit={submit} className="space-y-6">
                    <FormField id="name" label="Nom de l’organisation" error={form.errors.name}>
                        <input id="name" maxLength={255} required value={form.data.name} onChange={event => form.setData('name', event.currentTarget.value)} className={inputClass} />
                    </FormField>
                    {organization?.logoUrl && <div className="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-black/10"><img src={organization.logoUrl} alt={`Logo de ${organization.name}`} className="max-h-24 max-w-48 object-contain" /></div>}
                    <FormField id="logo" label="Logo de l’organisation" hint="JPG, PNG ou WebP · 2 Mo maximum" error={form.errors.logo}>
                        <input id="logo" type="file" accept="image/jpeg,image/png,image/webp" required={!isEditing} onChange={event => form.setData('logo', event.currentTarget.files?.[0] ?? null)} className={inputClass} />
                    </FormField>
                    {form.progress && <div className="h-2 overflow-hidden rounded-full bg-slate-100 dark:bg-white/10"><div className="h-full bg-brand-600 transition-all" style={{ width: `${form.progress.percentage}%` }} /></div>}
                    <div className="flex flex-wrap gap-3">
                        <PrimaryButton disabled={form.processing}>{form.processing ? 'Enregistrement…' : 'Enregistrer l’organisation'}</PrimaryButton>
                        <Link href={organization ? `/admin/organizations/${organization.id}` : '/admin/organizations'} className="rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-white/15 dark:text-slate-200 dark:hover:bg-white/5">Annuler</Link>
                    </div>
                </form>
            </section>
        </AppLayout>
    );
}

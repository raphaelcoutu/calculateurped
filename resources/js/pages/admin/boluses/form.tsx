import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import AppLayout from '@/components/app-layout';
import { FormField, inputClass, PrimaryButton } from '@/components/form-field';

type Bolus = {
    id: number;
    name: string;
    brand_name: string;
    unit: string;
    commercial_concentration: number;
    dosage: number;
    minimum_dose: number;
    maximum_dose: number;
    dose_precision: number;
    volume_precision: number;
    type: number;
    min_weight: number;
    max_weight: number;
    instructions: string;
    asterisk: boolean;
} | null;

type BolusFormData = {
    name: string;
    brand_name: string;
    unit: string;
    commercial_concentration: string;
    dosage: string;
    minimum_dose: string;
    maximum_dose: string;
    dose_precision: string;
    volume_precision: string;
    type: string;
    min_weight: string;
    max_weight: string;
    instructions: string;
    asterisk: boolean;
};

const fields: { id: Exclude<keyof BolusFormData, 'asterisk' | 'instructions'>; label: string; type?: string; hint?: string }[] = [
    { id: 'name', label: 'Nom du bolus' },
    { id: 'brand_name', label: 'Nom commercial', hint: 'Facultatif' },
    { id: 'unit', label: 'Unité de dose', hint: 'Ex.: mg, g, mmol, mL' },
    { id: 'commercial_concentration', label: 'Concentration commerciale (par mL)', type: 'number' },
    { id: 'dosage', label: 'Dose par kg', type: 'number' },
    { id: 'minimum_dose', label: 'Dose minimale', type: 'number' },
    { id: 'maximum_dose', label: 'Dose maximale', type: 'number' },
    { id: 'dose_precision', label: 'Précision de dose', type: 'number' },
    { id: 'volume_precision', label: 'Précision de volume', type: 'number' },
    { id: 'min_weight', label: 'Poids minimal (kg)', type: 'number' },
    { id: 'max_weight', label: 'Poids maximal (kg)', type: 'number' },
];

export default function BolusForm({ bolus, action, method }: {
    bolus: Bolus;
    action: string;
    method: 'post' | 'put';
}) {
    const form = useForm<BolusFormData>({
        name: bolus?.name ?? '',
        brand_name: bolus?.brand_name ?? '',
        unit: bolus?.unit ?? 'mg',
        commercial_concentration: String(bolus?.commercial_concentration ?? ''),
        dosage: String(bolus?.dosage ?? ''),
        minimum_dose: String(bolus?.minimum_dose ?? '0'),
        maximum_dose: String(bolus?.maximum_dose ?? ''),
        dose_precision: String(bolus?.dose_precision ?? '1'),
        volume_precision: String(bolus?.volume_precision ?? '1'),
        type: String(bolus?.type ?? '1'),
        min_weight: String(bolus?.min_weight ?? '0'),
        max_weight: String(bolus?.max_weight ?? '999'),
        instructions: bolus?.instructions ?? '',
        asterisk: bolus?.asterisk ?? false,
    });
    const isEditing = method === 'put';

    function submit(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();
        if (isEditing) {
            form.put(action);
            return;
        }

        form.post(action);
    }

    return (
        <AppLayout>
            <Head title={isEditing ? 'Modifier un brouillon de bolus' : 'Créer un bolus'} />
            <div className="mb-6">
                <Link href={bolus ? `/admin/boluses/${bolus.id}` : '/admin/boluses'} className="text-sm font-medium text-brand-700 hover:underline dark:text-brand-100">← Retour aux bolus</Link>
                <p className="mt-5 text-xs font-semibold uppercase tracking-[0.18em] text-brand-700 dark:text-brand-100">Gestion des recettes</p>
                <h1 className="mt-2 text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">{isEditing ? `Modifier ${bolus?.name}` : 'Créer un bolus en brouillon'}</h1>
                <p className="mt-2 text-sm text-slate-600 dark:text-slate-300">Le brouillon demeure privé à votre organisation jusqu’à sa publication.</p>
            </div>
            <section className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#182524] sm:p-7">
                <form onSubmit={submit} className="space-y-6">
                    <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        {fields.map(field => <FormField key={field.id} id={field.id} label={field.label} hint={field.hint} error={form.errors[field.id]}>
                            <input id={field.id} type={field.type ?? 'text'} step={field.type === 'number' ? 'any' : undefined} required={field.id !== 'brand_name'} value={form.data[field.id]} onChange={event => form.setData(field.id, event.currentTarget.value)} className={inputClass} />
                        </FormField>)}
                        <FormField id="type" label="Catégorie" error={form.errors.type}>
                            <select id="type" value={form.data.type} onChange={event => form.setData('type', event.currentTarget.value)} className={inputClass}>
                                <option value="1">IV direct</option>
                                <option value="2">Intubation séquence rapide</option>
                                <option value="3">Autres médicaments</option>
                            </select>
                        </FormField>
                    </div>
                    <FormField id="instructions" label="Instructions" hint="Facultatif · 191 caractères maximum" error={form.errors.instructions}>
                        <textarea id="instructions" maxLength={191} rows={4} value={form.data.instructions} onChange={event => form.setData('instructions', event.currentTarget.value)} className={inputClass} />
                    </FormField>
                    <label className="flex items-center gap-3 text-sm font-medium text-slate-700 dark:text-slate-200">
                        <input type="checkbox" checked={form.data.asterisk} onChange={event => form.setData('asterisk', event.currentTarget.checked)} className="size-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500" />
                        Afficher un astérisque après le nom
                    </label>
                    <div className="flex flex-wrap gap-3">
                        <PrimaryButton disabled={form.processing}>{form.processing ? 'Enregistrement…' : 'Enregistrer le brouillon'}</PrimaryButton>
                        <Link href={bolus ? `/admin/boluses/${bolus.id}` : '/admin/boluses'} className="rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-white/15 dark:text-slate-200 dark:hover:bg-white/5">Annuler</Link>
                    </div>
                </form>
            </section>
        </AppLayout>
    );
}

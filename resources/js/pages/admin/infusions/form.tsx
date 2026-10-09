import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import AppLayout from '@/components/app-layout';
import { FormField, inputClass, PrimaryButton } from '@/components/form-field';
import type { Recipe, Gap } from './types';

const drugDefaults = {
    name: '', brand_name: '', concentration: '', debit_min: '0', debit_max: '0',
    dose_unit: 'mg/kg/h', debit_min_limit: '0', debit_max_limit: '0',
    debit_limit_unit: 'mg', dosage_precision: '1',
};
const preparationDefaults = {
    min_weight: '0', max_weight: '', concentration: '', concentration_unit: 'mg', total_volume: '', instructions: '',
};
type PreparationData = typeof preparationDefaults;
type DrugKey = keyof typeof drugDefaults;
type PreparationKey = keyof PreparationData;
const drugFields: { id: DrugKey; label: string; numeric?: boolean; options?: string[]; hint?: string }[] = [
    { id: 'name', label: 'Nom du médicament' }, { id: 'brand_name', label: 'Nom commercial (facultatif)' },
    { id: 'concentration', label: 'Concentration commerciale (ex. : 1 mg/mL)' },
    { id: 'debit_min', label: 'Dose minimale', numeric: true },
    { id: 'debit_max', label: 'Dose maximale', hint: '0 si aucune limite', numeric: true },
    { id: 'dose_unit', label: 'Unité de dose' },
    { id: 'debit_min_limit', label: 'Débit minimal', hint: '0 si aucune limite', numeric: true },
    { id: 'debit_max_limit', label: 'Débit maximal', hint: '0 si aucune limite', numeric: true },
    { id: 'debit_limit_unit', label: 'Unité de débit', hint: 'Par heure', options: ['mg', 'mcg', 'unité', 'mU'] },
    { id: 'dosage_precision', label: 'Décimales des doses', numeric: true },
];
const preparationFields: { id: PreparationKey; label: string; numeric?: boolean; options?: string[] }[] = [
    { id: 'min_weight', label: 'Poids minimal inclus (kg)', numeric: true },
    { id: 'max_weight', label: 'Poids maximal exclu (kg), vide si absent', numeric: true },
    { id: 'concentration', label: 'Concentration', numeric: true },
    { id: 'concentration_unit', label: 'Unité de concentration', options: ['mg', 'mcg', 'unité', 'mU'] },
    { id: 'total_volume', label: 'Volume total (mL)', numeric: true },
];

export default function InfusionForm({ infusion, action, method, doseUnits }: { infusion: Recipe | null; action: string; method: 'post' | 'put'; doseUnits: string[] }) {
    const initial = { ...drugDefaults };
    for (const key of Object.keys(initial) as DrugKey[]) {
        initial[key] = String(infusion?.[key] ?? drugDefaults[key]);
    }
    initial.debit_limit_unit = infusion?.debit_limit_unit || infusion?.debit_dose_unit || drugDefaults.debit_limit_unit;
    const form = useForm({ ...initial, preparations: infusion?.preparations.map(preparation => {
        const data = { ...preparationDefaults };
        for (const key of Object.keys(data) as PreparationKey[]) data[key] = String(preparation[key] ?? '');
        return data;
    }) ?? [{ ...preparationDefaults }] });
    const errors = form.errors as Record<string, string>;
    const gaps = coverageGaps(form.data.preparations);

    function submit(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();
        form.submit(method, action);
    }
    function move(index: number, direction: number) {
        const preparations = [...form.data.preparations];
        [preparations[index], preparations[index + direction]] = [preparations[index + direction], preparations[index]];
        form.setData('preparations', preparations);
        form.clearErrors();
    }
    function updatePreparation(index: number, key: PreparationKey, value: string) {
        form.setData('preparations', form.data.preparations.map((preparation, position) => position === index ? { ...preparation, [key]: value } : preparation));
    }

    return <AppLayout>
        <Head title={infusion ? 'Modifier une perfusion' : 'Créer une perfusion'} />
        <Link href={infusion ? `/admin/infusions/${infusion.id}` : '/admin/infusions'} className="text-sm text-brand-700 dark:text-brand-100">← Retour aux perfusions</Link>
        <h1 className="mt-5 text-3xl font-semibold">{infusion ? `Modifier ${infusion.name}` : 'Créer une fiche de perfusion'}</h1>
        <p className="mt-2 text-sm text-slate-600 dark:text-slate-300">Le médicament et ses préparations sont enregistrés ensemble dans un brouillon privé à votre centre.</p>
        <form onSubmit={submit} className="mt-6 space-y-6">
            <section className="rounded-2xl border border-slate-200 bg-white p-5 dark:border-white/10 dark:bg-[#182524]">
                <h2 className="mb-5 text-lg font-semibold">Médicament</h2>
                <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    {drugFields.map(field => {
                        const options = field.id === 'dose_unit' ? doseUnits : field.options;
                        const hint = ['debit_min', 'debit_max'].includes(field.id) ? `${form.data.dose_unit}${field.hint ? ` · ${field.hint}` : ''}` : field.hint;
                        return <FormField key={field.id} id={field.id} label={field.label} hint={hint} error={errors[field.id]}>
                        {options ? <select id={field.id} className={inputClass} value={form.data[field.id]} onChange={event => form.setData(field.id, event.target.value)}>{options.map(option => <option key={option}>{option}</option>)}</select>
                            : <input id={field.id} className={inputClass} type={field.numeric ? 'number' : 'text'} step="any" min={field.numeric ? 0 : undefined} required={field.id !== 'brand_name'} value={form.data[field.id]} onChange={event => form.setData(field.id, event.target.value)} />}
                    </FormField>;
                    })}
                </div>
            </section>
            <section className="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 dark:border-white/10 dark:bg-[#182524]">
                <h2 className="text-lg font-semibold">Préparations selon le poids</h2>
                <p className="text-sm text-slate-600 dark:text-slate-300">Le calcul retient la première préparation compatible, dans l’ordre ci-dessous. Les chevauchements sont permis.</p>
                {gaps.length > 0 && <div role="status" className="rounded-xl bg-amber-50 p-4 text-sm text-amber-900 dark:bg-amber-950 dark:text-amber-100">Plages non couvertes entre 0 et 100 kg : {gaps.map(gap => `[${gap.min}, ${gap.max}[ kg`).join(', ')}. Vous pouvez enregistrer et publier malgré cet avertissement.</div>}
                {errors.preparations && <p role="alert" className="text-rose-700 dark:text-rose-300">{errors.preparations}</p>}
                {form.data.preparations.map((preparation, index) => <fieldset key={index} className="rounded-xl border border-slate-200 p-4 dark:border-white/15">
                    <legend className="px-2 font-semibold">Préparation {index + 1}</legend>
                    <div className="mb-4 flex flex-wrap gap-3 text-sm text-brand-700 dark:text-brand-100">
                        <button type="button" disabled={index === 0} onClick={() => move(index, -1)} className="disabled:opacity-30">Monter</button>
                        <button type="button" disabled={index === form.data.preparations.length - 1} onClick={() => move(index, 1)} className="disabled:opacity-30">Descendre</button>
                        <button type="button" disabled={form.data.preparations.length === 1} onClick={() => { form.setData('preparations', form.data.preparations.filter((_, position) => position !== index)); form.clearErrors(); }} className="disabled:opacity-30">Retirer</button>
                    </div>
                    {errors[`preparations.${index}`] && <p role="alert" className="mb-3 text-sm text-rose-700 dark:text-rose-300">{errors[`preparations.${index}`]}</p>}
                    <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">{preparationFields.map(field => {
                        const id = `preparations.${index}.${field.id}`;
                        return <FormField key={field.id} id={id} label={field.label} error={errors[id]}>
                            {field.options ? <select id={id} className={inputClass} value={preparation[field.id]} onChange={event => updatePreparation(index, field.id, event.target.value)}>{field.options.map(option => <option key={option}>{option}</option>)}</select>
                                : <input id={id} className={inputClass} type="number" min="0" step="any" required={field.id !== 'max_weight'} value={preparation[field.id]} onChange={event => updatePreparation(index, field.id, event.target.value)} />}
                        </FormField>;
                    })}</div>
                    <div className="mt-4"><FormField id={`instructions-${index}`} label="Instructions de préparation" hint="191 caractères maximum" error={errors[`preparations.${index}.instructions`]}><textarea id={`instructions-${index}`} rows={3} maxLength={191} className={inputClass} value={preparation.instructions} onChange={event => updatePreparation(index, 'instructions', event.target.value)} /></FormField></div>
                </fieldset>)}
                <button type="button" disabled={form.data.preparations.length >= 100} onClick={() => form.setData('preparations', [...form.data.preparations, { ...preparationDefaults }])} className="text-sm font-semibold text-brand-700 disabled:opacity-30 dark:text-brand-100">+ Ajouter une préparation</button>
            </section>
            <PrimaryButton disabled={form.processing}>{form.processing ? 'Enregistrement…' : 'Enregistrer le brouillon'}</PrimaryButton>
        </form>
    </AppLayout>;
}

function coverageGaps(preparations: PreparationData[]): Gap[] {
    const gaps: Gap[] = [];
    let coveredUntil = 0;
    const ranges = preparations.filter(preparation => preparation.min_weight !== '' && Number.isFinite(Number(preparation.min_weight)) && (preparation.max_weight === '' || Number(preparation.max_weight) > Number(preparation.min_weight)))
        .map(preparation => ({ min: Math.max(0, Number(preparation.min_weight)), max: preparation.max_weight === '' ? 100 : Number(preparation.max_weight) }))
        .sort((a, b) => a.min - b.min);
    for (const range of ranges) {
        const min = Math.min(100, range.min);
        if (min > coveredUntil) gaps.push({ min: coveredUntil, max: min });
        coveredUntil = Math.max(coveredUntil, Math.min(100, range.max));
    }
    if (coveredUntil < 100) gaps.push({ min: coveredUntil, max: 100 });
    return gaps;
}

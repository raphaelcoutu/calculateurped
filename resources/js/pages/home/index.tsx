import { Head } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import AppLayout from '@/components/app-layout';
import AgeSexHelper from '@/helpers/age-sex-helper';
import BroselowHelper from '@/helpers/broselow-helper';

type Mode = 'kg' | 'lb' | 'age' | 'broselow';

const ageOptions = [
    ...Array.from({ length: 24 }, (_, index) => ({ value: `month_${index + 1}`, label: `${index + 1} mois` })),
    ...Array.from({ length: 16 }, (_, index) => ({ value: `year_${index + 3}`, label: `${index + 3} ans` })),
];

const broselowColors = [
    ['grey', 'Gris', 'bg-slate-300'], ['pink', 'Rose', 'bg-pink-300'], ['red', 'Rouge', 'bg-red-300'],
    ['purple', 'Mauve', 'bg-violet-300'], ['yellow', 'Jaune', 'bg-yellow-200'], ['white', 'Blanc', 'bg-white'],
    ['blue', 'Bleu', 'bg-blue-300'], ['orange', 'Orange', 'bg-orange-300'], ['green', 'Vert', 'bg-emerald-300'],
] as const;

const modeOptions: { id: Mode; label: string; detail: string }[] = [
    { id: 'kg', label: 'Kilogrammes', detail: 'Poids mesuré' },
    { id: 'lb', label: 'Livres', detail: 'Conversion en kg' },
    { id: 'age', label: 'Âge et sexe', detail: 'Poids estimé' },
    { id: 'broselow', label: 'Broselow', detail: 'Poids estimé' },
];

function sanitizeNumber(value: string) {
    const clean = value.replace(/[^\d.,]/g, '').replace(/,/g, '.');
    const [whole, ...decimals] = clean.split('.');
    return decimals.length ? `${whole}.${decimals.join('').slice(0, 2)}` : whole;
}

export default function Home({ form }: { form: { name: string; id: string } }) {
    const [mode, setMode] = useState<Mode>('kg');
    const [rawWeight, setRawWeight] = useState('');
    const [estimated, setEstimated] = useState(false);
    const [sex, setSex] = useState('');
    const [age, setAge] = useState('');
    const [color, setColor] = useState('');

    const weight = useMemo(() => {
        if (mode === 'age' && age && sex) return AgeSexHelper.get(age, sex);
        if (mode === 'broselow' && color) return BroselowHelper.get(color);
        if (mode === 'lb' && rawWeight) return (Number(rawWeight) / 2.2).toFixed(2);
        return rawWeight;
    }, [age, color, mode, rawWeight, sex]);

    const validWeight = Number(weight) >= 2 && Number(weight) <= 250;

    function changeMode(nextMode: Mode) {
        setMode(nextMode);
        setRawWeight('');
        setEstimated(nextMode === 'age' || nextMode === 'broselow');
        setSex('');
        setAge('');
        setColor('');
    }

    function reset() {
        setRawWeight('');
        setEstimated(false);
        setSex('');
        setAge('');
        setColor('');
    }

    return (
        <AppLayout>
            <Head title="Calculateur de doses pédiatriques" />
            <section className="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#182524]">
                <div className="border-b border-slate-100 bg-gradient-to-r from-brand-50 to-white px-5 py-7 dark:border-white/10 dark:from-brand-900/40 dark:to-[#182524] sm:px-8 sm:py-9">
                    <p className="text-xs font-semibold uppercase tracking-[0.18em] text-brand-700 dark:text-brand-100">Outil clinique pédiatrique</p>
                    <h1 className="mt-3 max-w-3xl text-2xl font-semibold leading-tight tracking-tight text-slate-950 dark:text-white sm:text-3xl">Calculateur de doses pour les urgences et les soins intensifs pédiatriques</h1>
                    <p className="mt-3 max-w-2xl text-sm leading-6 text-slate-600 dark:text-slate-300">Saisissez les renseignements du patient et choisissez la méthode de détermination du poids.</p>
                </div>
                <form action="/" method="post" autoComplete="off" className="space-y-8 p-5 sm:p-8">
                    <input type="hidden" name="_token" value={usePageCsrfToken()} />
                    <div className="grid gap-5 sm:grid-cols-2">
                        <label className="block text-sm font-medium text-slate-700 dark:text-slate-200">Nom, prénom<input name="name" defaultValue={form.name} className="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-sm shadow-sm outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-white/15 dark:bg-[#0f1918] dark:text-white" /></label>
                        <label className="block text-sm font-medium text-slate-700 dark:text-slate-200">Numéro de dossier<input name="id" defaultValue={form.id} className="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-sm shadow-sm outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-white/15 dark:bg-[#0f1918] dark:text-white" /></label>
                    </div>

                    <fieldset>
                        <legend className="text-sm font-semibold text-slate-900 dark:text-white">Détermination du poids</legend>
                        <div className="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-4">
                            {modeOptions.map(option => <button type="button" key={option.id} onClick={() => changeMode(option.id)} className={`rounded-xl border px-3 py-3 text-left transition ${mode === option.id ? 'border-brand-500 bg-brand-50 ring-2 ring-brand-500/10 dark:border-brand-500 dark:bg-brand-900/40' : 'border-slate-200 bg-white hover:bg-slate-50 dark:border-white/10 dark:bg-[#142120] dark:hover:bg-white/5'}`}><span className="block text-sm font-semibold text-slate-900 dark:text-white">{option.label}</span><span className="mt-1 block text-xs text-slate-500 dark:text-slate-400">{option.detail}</span></button>)}
                        </div>
                    </fieldset>

                    <div className="grid min-h-32 gap-5 rounded-2xl bg-slate-50 p-5 dark:bg-black/15 sm:grid-cols-2 sm:p-6">
                        {(mode === 'kg' || mode === 'lb') && <div>
                            <label htmlFor="weight_entry" className="block text-sm font-medium text-slate-700 dark:text-slate-200">Poids en {mode === 'kg' ? 'kilogrammes' : 'livres'}</label>
                            <div className="mt-2 flex"><input id="weight_entry" type="text" inputMode="decimal" value={rawWeight} onChange={event => setRawWeight(sanitizeNumber(event.currentTarget.value))} min={mode === 'kg' ? 2 : 4.4} max={mode === 'kg' ? 250 : 550} className="block w-full rounded-l-xl border border-slate-300 bg-white px-3.5 py-3 text-sm outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-white/15 dark:bg-[#0f1918] dark:text-white" /><span className="rounded-r-xl border border-l-0 border-slate-300 bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-600 dark:border-white/15 dark:bg-white/5 dark:text-slate-300">{mode}</span></div>
                            {mode === 'lb' && weight && <p className="mt-2 text-sm text-slate-500 dark:text-slate-400">Poids converti : {weight} kg</p>}
                            {Number(rawWeight) > 0 && !validWeight && <p className="mt-2 text-sm text-rose-700 dark:text-rose-300">Le poids doit être entre 2 et 250 kg.</p>}
                            <label className="mt-4 inline-flex items-center gap-2.5 text-sm text-slate-600 dark:text-slate-300"><input type="checkbox" checked={estimated} onChange={event => setEstimated(event.currentTarget.checked)} className="size-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500" /> Poids estimé</label>
                        </div>}
                        {mode === 'age' && <>
                            <label className="block text-sm font-medium text-slate-700 dark:text-slate-200">Sexe<select value={sex} onChange={event => setSex(event.currentTarget.value)} className="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-sm dark:border-white/15 dark:bg-[#0f1918] dark:text-white"><option value="">Sélectionnez…</option><option value="male">Masculin</option><option value="female">Féminin</option></select></label>
                            <label className="block text-sm font-medium text-slate-700 dark:text-slate-200">Âge<select value={age} onChange={event => setAge(event.currentTarget.value)} className="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-sm dark:border-white/15 dark:bg-[#0f1918] dark:text-white"><option value="">Sélectionnez…</option>{ageOptions.map(option => <option key={option.value} value={option.value}>{option.label}</option>)}</select></label>
                        </>}
                        {mode === 'broselow' && <fieldset className="text-sm font-medium text-slate-700 dark:text-slate-200">
                            <legend>Échelle de Broselow</legend>
                            <div className="mt-2 grid grid-cols-3 gap-2">
                                {broselowColors.map(([value, label, swatchClass]) => <button key={value} type="button" aria-pressed={color === value} onClick={() => setColor(value)} className={`flex items-center gap-2 rounded-xl border px-2.5 py-2.5 text-left text-xs font-semibold transition focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 dark:focus:ring-offset-[#142120] ${color === value ? 'border-brand-600 bg-brand-50 ring-2 ring-brand-500/20 dark:border-brand-500 dark:bg-brand-900/40' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50 dark:border-white/10 dark:bg-[#0f1918] dark:hover:bg-white/5'}`}>
                                    <span aria-hidden="true" className={`size-6 shrink-0 rounded-md border border-black/10 ${swatchClass} ${value === 'white' ? 'border-slate-300' : ''}`} />
                                    <span className="text-slate-800 dark:text-slate-100">{label}</span>
                                </button>)}
                            </div>
                            {color && <p className="mt-2 text-xs text-slate-600 dark:text-slate-300">Couleur sélectionnée : <span className="font-semibold">{broselowColors.find(([value]) => value === color)?.[1]}</span></p>}
                        </fieldset>}
                        {weight && validWeight && <div className="flex items-center gap-4 rounded-xl border border-brand-100 bg-white p-4 dark:border-brand-700 dark:bg-[#142120]">
                            <span className="grid size-11 place-items-center rounded-full bg-brand-50 text-brand-700 dark:bg-brand-900/60 dark:text-brand-100">⚖</span>
                            <span>
                                <span className="block text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Poids utilisé</span>
                                <span className="mt-0.5 block text-xl font-semibold text-slate-950 dark:text-white">{weight} kg</span>
                                {mode === 'broselow' && color && <span className="mt-1 inline-flex items-center gap-2 text-xs text-slate-600 dark:text-slate-300">
                                    <span aria-hidden="true" className={`size-3 rounded-full border border-black/10 ${broselowColors.find(([value]) => value === color)?.[2]} ${color === 'white' ? 'border-slate-300' : ''}`} />
                                    Bande {broselowColors.find(([value]) => value === color)?.[1]}
                                </span>}
                            </span>
                        </div>}
                    </div>
                    <input type="hidden" name="weight" value={weight || 0} />
                    <input type="hidden" name="estimated" value={estimated ? 'true' : 'false'} />
                    <div className="flex flex-wrap gap-3">
                        <button type="submit" disabled={!validWeight} className="rounded-xl bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:text-slate-500 dark:disabled:bg-slate-700 dark:disabled:text-slate-400">Calculer les doses <span aria-hidden="true">→</span></button>
                        <button type="button" onClick={reset} className="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-white/15 dark:text-slate-200 dark:hover:bg-white/5">Réinitialiser le poids</button>
                    </div>
                </form>
            </section>
            <section className="mt-6 grid gap-4 lg:grid-cols-2">
                <div className="rounded-2xl border-l-4 border-rose-500 bg-rose-50 p-5 text-sm leading-6 text-rose-950 dark:bg-rose-950/40 dark:text-rose-100"><p className="font-bold">Attention à la concentration commerciale</p><p className="mt-2">La concentration utilisée doit correspondre à celle indiquée par le calculateur pour que la conversion de mg à mL soit exacte. Il est possible de prescrire en mg avec une autre concentration, en ajustant le volume à administrer.</p></div>
                <div className="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm leading-6 text-amber-950 dark:border-amber-900 dark:bg-amber-950/30 dark:text-amber-100"><p className="font-bold">Outil d’aide à la décision</p><p className="mt-2">Ce calculateur ne constitue pas une prescription. Le jugement de l’équipe médicale doit s’appliquer en tout temps. Les doses suggérées ne s’appliquent pas à la population néonatale; les doses et volumes sont arrondis pour faciliter l’administration.</p></div>
            </section>
        </AppLayout>
    );
}

function usePageCsrfToken() {
    return document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';
}

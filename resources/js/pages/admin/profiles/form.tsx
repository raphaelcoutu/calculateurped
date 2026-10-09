import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import AppLayout from '@/components/app-layout';
import { FormField, inputClass, PrimaryButton } from '@/components/form-field';
import type { Item, Section, RecipeChoice } from './types';
import { buttonClass, panelClass } from './types';

export default function ProfileForm({ profile, recipes, action, method }: {
    profile: { name: string; sections: Section[] } | null;
    recipes: RecipeChoice[]; action: string; method: 'post' | 'put';
}) {
    const form = useForm({ name: profile?.name ?? '', sections: profile?.sections ?? [{ name: '', items: [] }] as Section[] });
    const errors = form.errors as Record<string, string>;
    function setSection(index: number, section: Section) {
        form.setData('sections', form.data.sections.map((current, position) => position === index ? section : current));
        form.clearErrors();
    }
    function moveSection(index: number, direction: number) {
        const sections = [...form.data.sections];
        [sections[index], sections[index + direction]] = [sections[index + direction], sections[index]];
        form.setData('sections', sections);
        form.clearErrors();
    }
    function moveItem(sectionIndex: number, index: number, direction: number) {
        const section = form.data.sections[sectionIndex];
        const items = [...section.items];
        [items[index], items[index + direction]] = [items[index + direction], items[index]];
        setSection(sectionIndex, { ...section, items });
    }
    function submit(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();
        form.submit(method, action);
    }
    return <AppLayout>
        <Head title={profile ? 'Modifier le profil' : 'Créer un profil'} />
        <Link href="/admin/profiles" className="text-sm text-brand-700 dark:text-brand-100">← Retour aux profils</Link>
        <h1 className="mt-5 text-3xl font-semibold">{profile ? 'Modifier le profil' : 'Créer un profil d’ordonnance'}</h1>
        <p className="mt-2 text-sm text-slate-600 dark:text-slate-300">Organisez les sections et les recettes dans l’ordre du PDF. Seules les recettes publiées peuvent être ajoutées. Une recette peut apparaître plusieurs fois.</p>
        <form onSubmit={submit} className="mt-6 flex flex-col gap-5">
            <fieldset disabled={form.processing} className="flex flex-col gap-5 disabled:opacity-60">
                <div className={panelClass}><FormField id="profile-name" label="Nom du profil" error={errors.name}>
                    <input id="profile-name" className={inputClass} value={form.data.name} onChange={event => form.setData('name', event.target.value)} required maxLength={191} />
                </FormField></div>
                {errors.sections && <p role="alert" className="text-sm text-rose-700 dark:text-rose-300">{errors.sections}</p>}
                {form.data.sections.map((section, sectionIndex) => <section key={sectionIndex} className={panelClass}>
                    <div className="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <h2 className="font-semibold">Section {sectionIndex + 1}</h2>
                        <div className="flex flex-wrap gap-2">
                            <button type="button" className={buttonClass} disabled={sectionIndex === 0} onClick={() => moveSection(sectionIndex, -1)} aria-label={`Monter la section ${sectionIndex + 1}`}>↑</button>
                            <button type="button" className={buttonClass} disabled={sectionIndex === form.data.sections.length - 1} onClick={() => moveSection(sectionIndex, 1)} aria-label={`Descendre la section ${sectionIndex + 1}`}>↓</button>
                            <button type="button" className={buttonClass} onClick={() => { form.setData('sections', form.data.sections.filter((_, index) => index !== sectionIndex)); form.clearErrors(); }}>Supprimer la section</button>
                        </div>
                    </div>
                    <FormField id={`section-${sectionIndex}`} label="Nom de la section" error={errors[`sections.${sectionIndex}.name`]}>
                        <input id={`section-${sectionIndex}`} className={inputClass} value={section.name} onChange={event => setSection(sectionIndex, { ...section, name: event.target.value })} required maxLength={191} />
                    </FormField>
                    <ol className="mt-4 flex flex-col gap-3">{section.items.map((item, itemIndex) => <li key={itemIndex} className="flex flex-wrap items-end gap-3 rounded-xl bg-slate-50 p-3 dark:bg-black/15">
                        <div className="min-w-48 flex-1"><FormField id={`recipe-${sectionIndex}-${itemIndex}`} label={`Recette ${itemIndex + 1}`} error={errors[`sections.${sectionIndex}.items.${itemIndex}.recipe_id`] ?? errors[`sections.${sectionIndex}.items.${itemIndex}.type`]}>
                            <select id={`recipe-${sectionIndex}-${itemIndex}`} className={inputClass} value={`${item.type}:${item.recipe_id}`} onChange={event => {
                                const [type, id] = event.target.value.split(':');
                                const changed: Item = { type: type as Item['type'], recipe_id: Number(id) };
                                setSection(sectionIndex, { ...section, items: section.items.map((current, index) => index === itemIndex ? changed : current) });
                            }}>
                                {!recipes.some(recipe => recipe.type === item.type && recipe.recipe_id === item.recipe_id) && <option value={`${item.type}:${item.recipe_id}`}>Recette déjà sélectionnée, indisponible pour un nouvel ajout</option>}
                                {recipes.map(recipe => <option key={`${recipe.type}:${recipe.recipe_id}`} value={`${recipe.type}:${recipe.recipe_id}`}>{recipe.type === 'bolus' ? 'Bolus' : 'Perfusion'} · {recipe.name} · v{recipe.version} · {recipe.status === 'published' ? 'Publié' : 'Brouillon'}</option>)}
                            </select>
                        </FormField></div>
                        <button type="button" className={buttonClass} disabled={itemIndex === 0} aria-label={`Monter la recette ${itemIndex + 1} de la section ${sectionIndex + 1}`} onClick={() => moveItem(sectionIndex, itemIndex, -1)}>↑</button>
                        <button type="button" className={buttonClass} disabled={itemIndex === section.items.length - 1} aria-label={`Descendre la recette ${itemIndex + 1} de la section ${sectionIndex + 1}`} onClick={() => moveItem(sectionIndex, itemIndex, 1)}>↓</button>
                        <button type="button" className={buttonClass} onClick={() => setSection(sectionIndex, { ...section, items: section.items.filter((_, index) => index !== itemIndex) })}>Retirer</button>
                    </li>)}</ol>
                    {errors[`sections.${sectionIndex}.items`] && <p role="alert" className="mt-3 text-sm text-rose-700 dark:text-rose-300">{errors[`sections.${sectionIndex}.items`]}</p>}
                    <button type="button" className={`${buttonClass} mt-4`} disabled={!recipes.length} onClick={() => setSection(sectionIndex, { ...section, items: [...section.items, { type: recipes[0].type, recipe_id: recipes[0].recipe_id }] })}>Ajouter une recette</button>
                    {!recipes.length && <p className="mt-3 text-sm text-slate-500 dark:text-slate-400">Publiez d’abord une recette de bolus ou de perfusion dans votre centre.</p>}
                </section>)}
                <div className="flex flex-wrap gap-3">
                    <button type="button" className={buttonClass} onClick={() => { form.setData('sections', [...form.data.sections, { name: '', items: [] }]); form.clearErrors(); }}>Ajouter une section</button>
                    <PrimaryButton disabled={form.processing}>{form.processing ? 'Enregistrement…' : 'Enregistrer le brouillon'}</PrimaryButton>
                </div>
            </fieldset>
        </form>
    </AppLayout>;
}

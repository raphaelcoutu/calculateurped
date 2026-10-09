import { useEffect, useRef, useState } from 'react';
import type { KeyboardEvent } from 'react';
import { inputClass } from '@/components/form-field';
import type { Item, RecipeChoice } from './types';

function recipeLabel(recipe: RecipeChoice): string {
    return `${recipe.name} · ${recipe.type === 'bolus' ? 'Bolus' : 'Perfusion'} · v${recipe.version}`;
}

function searchableText(value: string): string {
    return value.normalize('NFD').replace(/\p{Diacritic}/gu, '').toLocaleLowerCase('fr');
}

export default function RecipeAutocomplete({ id, recipes, value, onSelect, disabled = false, error }: {
    id: string; recipes: RecipeChoice[]; value: Item | null;
    onSelect: (recipe: RecipeChoice) => void; disabled?: boolean; error?: boolean;
}) {
    const selected = recipes.find(recipe => recipe.type === value?.type && recipe.recipe_id === value.recipe_id);
    const selectedLabel = selected ? recipeLabel(selected) : value ? 'Recette déjà sélectionnée, indisponible pour un nouvel ajout' : '';
    const [query, setQuery] = useState(selectedLabel);
    const [open, setOpen] = useState(false);
    const [activeIndex, setActiveIndex] = useState(0);
    const listRef = useRef<HTMLUListElement>(null);
    const matches = recipes.filter(recipe => searchableText(recipeLabel(recipe)).includes(searchableText(query.trim())));
    const listId = `${id}-suggestions`;

    useEffect(() => {
        setQuery(selectedLabel);
        setOpen(false);
        setActiveIndex(0);
    }, [selectedLabel, value?.type, value?.recipe_id]);

    useEffect(() => {
        if (open) listRef.current?.children[activeIndex]?.scrollIntoView({ block: 'nearest' });
    }, [activeIndex, open]);

    function choose(recipe: RecipeChoice) {
        setQuery(value ? recipeLabel(recipe) : '');
        setOpen(false);
        onSelect(recipe);
    }
    function keyDown(event: KeyboardEvent<HTMLInputElement>) {
        if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
            event.preventDefault();
            if (!open) {
                setQuery('');
                setOpen(true);
                setActiveIndex(0);
            } else if (matches.length) {
                const direction = event.key === 'ArrowDown' ? 1 : -1;
                setActiveIndex(index => (index + direction + matches.length) % matches.length);
            }
        } else if (event.key === 'Enter' && open) {
            event.preventDefault();
            if (matches[activeIndex]) choose(matches[activeIndex]);
        } else if (event.key === 'Escape' && open) {
            event.preventDefault();
            event.stopPropagation();
            setOpen(false);
            setQuery(selectedLabel);
        }
    }

    return <div className="relative">
        <input id={id} type="text" role="combobox" autoComplete="off" aria-autocomplete="list" aria-haspopup="listbox" aria-expanded={open} aria-controls={open ? listId : undefined} aria-activedescendant={open && matches[activeIndex] ? `${listId}-${activeIndex}` : undefined} aria-invalid={error || undefined} disabled={disabled} className={inputClass} placeholder="Rechercher un bolus ou une perfusion…" value={query} onChange={event => {
            setQuery(event.target.value);
            setOpen(true);
            setActiveIndex(0);
        }} onFocus={() => {
            setQuery('');
            setOpen(true);
            setActiveIndex(0);
        }} onBlur={() => {
            setOpen(false);
            setQuery(selectedLabel);
        }} onKeyDown={keyDown} />
        {open && !disabled && <div className="absolute z-20 mt-1 w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg dark:border-white/15 dark:bg-[#182524]">
            <ul ref={listRef} id={listId} role="listbox" aria-label="Recettes publiées" className="max-h-64 overflow-y-auto overscroll-contain py-1">
                {matches.map((recipe, index) => <li id={`${listId}-${index}`} key={`${recipe.type}:${recipe.recipe_id}`} role="option" aria-selected={index === activeIndex} className={`cursor-pointer px-4 py-3 text-sm ${index === activeIndex ? 'bg-brand-50 text-brand-900 dark:bg-brand-950 dark:text-brand-100' : 'text-slate-900 dark:text-white'}`} onMouseEnter={() => setActiveIndex(index)} onPointerDown={event => event.preventDefault()} onClick={() => choose(recipe)}>
                    <span className="block font-medium">{recipe.name}</span><span className="mt-1 block text-xs text-slate-500 dark:text-slate-400">{recipe.type === 'bolus' ? 'Bolus' : 'Perfusion'} · Version {recipe.version}</span>
                </li>)}
            </ul>
            {!matches.length && <p role="status" className="px-4 py-3 text-sm text-slate-500 dark:text-slate-400">Aucune recette publiée ne correspond à votre recherche.</p>}
        </div>}
    </div>;
}

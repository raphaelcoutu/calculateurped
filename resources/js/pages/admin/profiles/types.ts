export type Item = { type: 'bolus' | 'infusion'; recipe_id: number };
export type Section = { name: string; items: Item[] };
export type RecipeChoice = Item & { name: string; status: string; version: number };
export type ProfileSummary = { id: number; name: string; version: number; status: string; isDefault: boolean; author: string | null; publishedAt: string | null };
export const statusLabel = (status: string) => status === 'published' ? 'Publié' : status === 'unpublished' ? 'Dépublié' : 'Brouillon';
export const buttonClass = 'rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold hover:bg-slate-100 disabled:opacity-40 dark:border-white/15 dark:hover:bg-white/10';
export const panelClass = 'rounded-2xl border border-slate-200 bg-white p-5 dark:border-white/10 dark:bg-[#182524]';

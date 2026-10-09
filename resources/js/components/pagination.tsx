import { Link } from '@inertiajs/react';

export type PageLink = { url: string | null; label: string; active: boolean };

export default function Pagination({ links, currentPage, lastPage, total, labels }: {
    links: PageLink[];
    currentPage: number;
    lastPage: number;
    total: number;
    labels: { singular: string; plural: string };
}) {
    return (
        <div className="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-5 py-4 text-sm dark:border-white/10">
            <span className="text-slate-500 dark:text-slate-400">{total} {total === 1 ? labels.singular : labels.plural} · Page {currentPage} sur {lastPage}</span>
            <nav className="flex gap-1" aria-label="Pagination">
                {links.map((link, index) => {
                    const label = link.label.replace(/<[^>]+>/g, '').replaceAll('&laquo;', '←').replaceAll('&raquo;', '→').replace('Previous', 'Précédent').replace('Next', 'Suivant');

                    return link.url ? (
                        <Link key={`${label}-${index}`} href={link.url} className={`rounded-lg px-3 py-2 ${link.active ? 'bg-brand-600 font-semibold text-white' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10'}`} aria-current={link.active ? 'page' : undefined}>{label}</Link>
                    ) : (
                        <span key={`${label}-${index}`} className="px-3 py-2 text-slate-300 dark:text-slate-600">{label}</span>
                    );
                })}
            </nav>
        </div>
    );
}

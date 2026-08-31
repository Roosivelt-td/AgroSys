import React from 'react';

const AgroLogoPremium = ({ src, title, subtitle }) => {
    return (
        <div className="flex items-center gap-4 group cursor-pointer transition-all duration-500 hover:scale-105">
            <div className="relative">
                <div className="absolute -inset-1 bg-gradient-to-r from-emerald-500 to-agri-green rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                <div className="relative w-16 h-16 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-center shadow-2xl border border-white/20 p-2">
                    <img
                        src={src}
                        alt="Logo"
                        className="w-full h-full object-contain filter drop-shadow-md group-hover:rotate-12 transition-transform duration-500"
                    />
                </div>
            </div>
            <div className="flex flex-col">
                <h2 className="text-3xl font-black text-slate-800 dark:text-white italic tracking-tighter uppercase leading-none">
                    {title}
                </h2>
                <p className="text-[9px] text-agri-green font-black uppercase tracking-[0.3em] mt-1.5 italic opacity-80">
                    {subtitle}
                </p>
            </div>
        </div>
    );
};

export default AgroLogoPremium;

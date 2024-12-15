import React from "react";

export default function Layout({ children }: { children: React.ReactNode }) {
    return (
        <main className="text-white bg-black-base">
            <div className="flex items-center justify-center">
                <div className="flex flex-col w-full h-screen max-w-md border">
                    <div className="flex-grow">{children}</div>
                    <div className="h-20 border"></div>
                </div>
            </div>
        </main>
    );
}

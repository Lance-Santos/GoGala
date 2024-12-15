import { AppSidebar } from "@/components/app-sidebar";
import { Separator } from "@/components/ui/separator";
import { Toaster } from "@/components/ui/toaster";
import {
    SidebarInset,
    SidebarProvider,
    SidebarTrigger,
} from "@/components/ui/sidebar";
import MenuBar from "@/components/menu-bar-sidebar";
import AppSidebarRight from "@/components/app-sidebar-right";
import Editor from "@/components/editor";
import React, { useRef, useState } from "react";

export default function Index(props) {
    const { eventData, layout, organization } = props;
    const editorRef = useRef<EditorHandle>(null);
    const [elements, setElements] = useState<Element[]>([]);
    const [selectedElement, setSelectedElement] = useState<Element | null>(
        null
    );

    const handleDragStart = (element: string) => {
        console.log(`Dragging element: ${element}`);
    };

    return (
        <SidebarProvider>
            <AppSidebar onDragStart={() => handleDragStart} eventData={eventData}/>
            <SidebarInset>
                <div className="flex flex-col flex-1 gap-4 p-4 pt-0">
                    <Editor
                        ref={editorRef}
                        onElementSelect={selectedElement}
                        eventData={eventData}
                        layout={layout}
                        organization={organization}
                    />
                </div>
            </SidebarInset>
            {/* <AppSidebarRight side="right" /> */}
            <Toaster />
        </SidebarProvider>
    );
}

export interface EditorHandle {
    scrollCenter: () => void;
    undo: () => void;
}

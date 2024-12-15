import React, { useState, useEffect } from "react";
import {
    Menubar,
    MenubarCheckboxItem,
    MenubarContent,
    MenubarItem,
    MenubarMenu,
    MenubarSeparator,
    MenubarSub,
    MenubarShortcut,
    MenubarSubContent,
    MenubarSubTrigger,
    MenubarTrigger,
} from "@/components/ui/menubar";
import { Link } from "@inertiajs/react";
// Define the types for the props passed into MenuBar
interface MenuBarProps {
    onCenterPreviewClick: () => void;
    onUndoClick: () => void;
    onRedoClick: () => void;
    onCopyClick: () => void;
    onPasteClick: () => void;
    onDuplicateClick: () => void;
    onDeleteClick: () => void;
    onDownloadClick: () => void,
    onSaveClick: () => void,
}

const MenuBar: React.FC<MenuBarProps> = ({onSaveClick,onCopyClick,onCenterPreviewClick,onUndoClick,onRedoClick,onDeleteClick,onPasteClick,onDuplicateClick,onDownloadClick}) => {
    const [isFullscreen, setIsFullscreen] = useState(false);

    // Toggle fullscreen mode
    const toggleFullscreen = () => {
        if (isFullscreen) {
            document.exitFullscreen?.() ||
                (
                    document as Document & { webkitExitFullscreen?: () => void }
                ).webkitExitFullscreen?.() ||
                (
                    document as Document & { msExitFullscreen?: () => void }
                ).msExitFullscreen?.();
        } else {
            document.documentElement.requestFullscreen?.() ||
                (
                    document.documentElement as HTMLElement & {
                        webkitRequestFullscreen?: () => void;
                    }
                ).webkitRequestFullscreen?.() ||
                (
                    document.documentElement as HTMLElement & {
                        msRequestFullscreen?: () => void;
                    }
                ).msRequestFullscreen?.();
        }
    };

    // Listen for fullscreen changes to sync checkbox
    useEffect(() => {
        const onFullscreenChange = () =>
            setIsFullscreen(
                !!(
                    document.fullscreenElement ||
                    (
                        document as Document & {
                            webkitFullscreenElement?: Element;
                        }
                    ).webkitFullscreenElement ||
                    (document as Document & { msFullscreenElement?: Element })
                        .msFullscreenElement
                )
            );

        document.addEventListener("fullscreenchange", onFullscreenChange);
        document.addEventListener("webkitfullscreenchange", onFullscreenChange);
        document.addEventListener("msfullscreenchange", onFullscreenChange);

        return () => {
            document.removeEventListener(
                "fullscreenchange",
                onFullscreenChange
            );
            document.removeEventListener(
                "webkitfullscreenchange",
                onFullscreenChange
            );
            document.removeEventListener(
                "msfullscreenchange",
                onFullscreenChange
            );
        };
    }, []);

    // Listen for F11 keypress to toggle fullscreen
    useEffect(() => {
        const onKeyDown = (event: KeyboardEvent) => {
            // For F11 (Fullscreen toggle)
            if (event.key === "F11") {
                event.preventDefault(); // Prevent default F11 action (like opening browser's fullscreen)
                toggleFullscreen(); // Custom function to toggle fullscreen
            }

            // Define keyboard shortcuts for actions
            if (event.key === "z" && event.ctrlKey) {
                event.preventDefault();
                onUndoClick(); // Ctrl + Z for Undo
            }
            if (event.key === "y" && event.ctrlKey) {
                event.preventDefault();
                onRedoClick(); // Ctrl + Y for Redo
            }
            if (event.key === "c" && event.ctrlKey) {
                event.preventDefault();
                onCopyClick(); // Ctrl + C for Copy
            }
            if (event.key === "v" && event.ctrlKey) {
                event.preventDefault();
                onPasteClick(); // Ctrl + V for Paste
            }
            if (event.key === "d" && event.ctrlKey) {
                event.preventDefault();
                onDuplicateClick(); // Ctrl + D for Duplicate
            }
            if (event.key === "Delete") {
                event.preventDefault();
                onDeleteClick(); // Delete key for Delete
            }
            if (event.key === "c" && event.shiftKey) {
                event.preventDefault();
                onCenterPreviewClick(); // Shift + C for Center Preview
            }
            if (event.key === "s" && event.ctrlKey) {
                event.preventDefault();
                onSaveClick();
            }
        };

        window.addEventListener("keydown", onKeyDown);

        return () => {
            window.removeEventListener("keydown", onKeyDown);
        };
    }, [
        onCenterPreviewClick,
        onUndoClick,
        onRedoClick,
        onCopyClick,
        onPasteClick,
        onDuplicateClick,
        onDeleteClick,
    ]);

    return (
        <Menubar>
            <MenubarMenu>
                <MenubarTrigger>File</MenubarTrigger>
                <MenubarContent>
                    <MenubarItem>
                        <Link href="#" onClick={onSaveClick} preserveState>
                            Save
                        </Link>
                    </MenubarItem>
                    <MenubarItem>Enable Auto Save</MenubarItem>
                    <MenubarSeparator />
                    <MenubarSub>
                        <MenubarSubTrigger>Handling</MenubarSubTrigger>
                        <MenubarSubContent>
                            <MenubarItem>Import</MenubarItem>
                            <MenubarItem onClick={onDownloadClick}>
                                Export
                            </MenubarItem>
                        </MenubarSubContent>
                    </MenubarSub>
                    <MenubarSeparator />
                    <MenubarItem>Print...</MenubarItem>
                </MenubarContent>
            </MenubarMenu>

            <MenubarMenu>
                <MenubarTrigger>Edit</MenubarTrigger>
                <MenubarContent>
                    <MenubarItem onClick={onUndoClick}>Undo</MenubarItem>
                    <MenubarItem onClick={onRedoClick}>Redo</MenubarItem>
                    <MenubarSeparator />
                    {/* <MenubarSub>
                        <MenubarSubTrigger>Find</MenubarSubTrigger>
                        <MenubarSubContent>
                            <MenubarItem>Search the web</MenubarItem>
                            <MenubarSeparator />
                            <MenubarItem>Find...</MenubarItem>
                            <MenubarItem>Find Next</MenubarItem>
                            <MenubarItem>Find Previous</MenubarItem>
                        </MenubarSubContent>
                    </MenubarSub> */}
                    <MenubarSeparator />
                    <MenubarItem onClick={onDuplicateClick}>
                        Duplicate
                    </MenubarItem>
                    <MenubarItem onClick={onCopyClick}>Copy</MenubarItem>
                    <MenubarItem onClick={onPasteClick}>Paste</MenubarItem>
                    <MenubarItem onClick={onDeleteClick}>Delete</MenubarItem>
                </MenubarContent>
            </MenubarMenu>

            <MenubarMenu>
                <MenubarTrigger>View</MenubarTrigger>
                <MenubarContent>
                    <MenubarItem onClick={onCenterPreviewClick}>
                        Center Preview{" "}
                        <MenubarShortcut>Ctrl/Cmd + T</MenubarShortcut>
                    </MenubarItem>
                    <MenubarSeparator />
                    <MenubarCheckboxItem
                        checked={isFullscreen}
                        onCheckedChange={toggleFullscreen}
                    >
                        Toggle Fullscreen <MenubarShortcut>F11</MenubarShortcut>
                    </MenubarCheckboxItem>
                </MenubarContent>
            </MenubarMenu>
        </Menubar>
    );
};

export default MenuBar;

"use client";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Separator } from "@/components/ui/separator";
import { Slider } from "@/components/ui/slider";
import { useToast } from "@/hooks/use-toast";
import {Inertia} from "@inertiajs/inertia";
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import {
    ContextMenu,
    ContextMenuCheckboxItem,
    ContextMenuContent,
    ContextMenuItem,
    ContextMenuLabel,
    ContextMenuRadioGroup,
    ContextMenuRadioItem,
    ContextMenuSeparator,
    ContextMenuShortcut,
    ContextMenuSub,
    ContextMenuSubContent,
    ContextMenuSubTrigger,
    ContextMenuTrigger,
} from "@/components/ui/context-menu";
import { Card, CardContent, CardHeader } from "@/components/ui/card";
import React, {
    useRef,
    useImperativeHandle,
    useState,
    useCallback,
    useEffect,
} from "react";
import { Link } from "@inertiajs/react";
import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from "@/components/ui/breadcrumb";
import Moveable from "react-moveable";
import InfiniteViewer from "react-infinite-viewer";
import Selecto from "react-selecto";
import { Button } from "@/components/ui/button";
import MenuBar from "@/components/menu-bar-sidebar";
export interface EditorHandle {
    scrollCenter: () => void;
}
interface EditorProps {
    onElementSelect: (element: Element | null) => void;
    eventData: any;
    layout: any;
    organization: any;
}
interface Element {
    id: number;
    type: string;
    label: string;
    name: string;
    posX: number;
    posY: number;
    width: number;
    height: number;
    rotation: number;
    seats: Seat[];
    prefix: string;
    category: string;
    ticket_id: number;
    ref: React.RefObject<HTMLDivElement>;
}

interface Seat {
    seatNumber: ReactNode;
    id: number;
    name: string;
    isClaimed: boolean;
    userID: number | null;
    userName: string | null;
}
interface EditorProps {
    onElementSelect: (element: Element | null) => void;
    eventData: any;
    layout: any;
    organization: any;
}
const Editor = React.forwardRef<EditorHandle, EditorProps>(
    ({ onElementSelect , eventData,layout,organization }, ref) => {
        const { toast } = useToast();
        const viewerRef = useRef<InfiniteViewer>(null);
        const moveableRef = useRef<Moveable>(null);
        const selectoRef = useRef<Selecto>(null);
        const containerRef = useRef<HTMLDivElement>(null);
        const [history, setHistory] = useState<Element[][]>([]);
        const [redoHistory, setRedoHistory] = useState<Element[][]>([]);
        const [targets, setTargets] = useState<Array<SVGElement | HTMLElement>>(
            []
        );
        const [activeSelect, setActiveSelect] = useState(false);
        const [sizeLink, setSizeLink] = useState(false);
        const [elements, setElements] = useState<Element[]>(
            layout ? JSON.parse(layout) : []
        );
        const [selectedElementId, setSelectedElementId] = useState<
            number | null
        >(null);
        const [clipboard, setClipboard] = useState<Element | null>(null);
        const handleElementClick = (id: number) => {
            const element = elements.find((el) => el.id === id);
            if (element) {
                onElementSelect(element);
                setSelectedElementId(id);
            }
        };

        const selectedElement = elements.find(
            (element) => element.id === selectedElementId
        );
        const scrollOptions = {
            container: () => viewerRef.current?.getContainer(),
            threshold: 20,
            getScrollPosition: () => {
                return [
                    viewerRef.current?.getScrollLeft({ absolute: true }) || 0,
                    viewerRef.current?.getScrollTop({ absolute: true }) || 0,
                ];
            },
        };
        const centerView = () => {
            viewerRef.current!.scrollCenter();
        };
        document.addEventListener("contextmenu", (event) =>
            event.preventDefault()
        );
        const duplicateElement = () => {
            if (selectedElementId !== null) {
                const elementToDuplicate = elements.find(
                    (el) => el.id === selectedElementId
                );
                if (elementToDuplicate) {
                    const newElement: Element = {
                        ...elementToDuplicate,
                        id: Date.now(),
                        name: `${elementToDuplicate.name} Copy`,
                        posX: elementToDuplicate.posX + 20,
                        posY: elementToDuplicate.posY + 20,
                        ref: React.createRef<HTMLDivElement>(),
                    };
                    setElements((prevElements) => {
                        const newElements = [...prevElements, newElement];
                        saveHistory(newElements);
                        return newElements;
                    });
                }
            }
        };

        const deleteElement = () => {
            const moveable = moveableRef.current!;
            if (selectedElementId !== null) {
                setElements((prevElements) => {
                    const newElements = prevElements.filter(
                        (el) => el.id !== selectedElementId
                    );
                    saveHistory(newElements);
                    return newElements;
                });
                setSelectedElementId(null);
                setActiveSelect(false);
            }
        };

        const copyElement = () => {
            if (selectedElementId !== null) {
                const elementToCopy = elements.find(
                    (el) => el.id === selectedElementId
                );
                if (elementToCopy) setClipboard(elementToCopy);
            }
        };

        const cutElement = () => {
            if (selectedElementId !== null) {
                const elementToCut = elements.find(
                    (el) => el.id === selectedElementId
                );
                if (elementToCut) {
                    setClipboard(elementToCut);
                    deleteElement();
                }
            }
        };
            console.log(eventData.tickets[0].id);

        const pasteElement = () => {
            if (clipboard) {
                const newElement: Element = {
                    ...clipboard,
                    id: Date.now(),
                    name: `${clipboard.name} Pasted`,
                    posX: clipboard.posX + 20,
                    posY: clipboard.posY + 20,
                    ref: React.createRef<HTMLDivElement>(),
                };
                setElements((prev) => [...prev, newElement]);
            }
        };
        interface Ticket {
            id: number;
            type: string;
            price: string;
            event_id: number;
            quantity: number;
            created_at: string;
            updated_at: string;
        }
        const handleInputChange = (
            e: React.ChangeEvent<HTMLSelectElement | HTMLInputElement>,
            property: keyof Element,
            selectedTicket: Ticket | null = null // Now accepting the full ticket object
        ) => {
            if (selectedElementId !== null) {
                if (property === "seats") {
                    const numberOfSeats = parseInt(e.target.value, 10);
                    if (!isNaN(numberOfSeats) && numberOfSeats > 0) {
                        const parentElement = elements.find(
                            (element) => element.id === selectedElementId
                        );
                        if (parentElement) {
                            const newSeats: Seat[] = Array.from(
                                { length: numberOfSeats },
                                (_, index) => ({
                                    id: Date.now() + index,
                                    name: `Seat ${index + 1}`,
                                    isClaimed: false,
                                    userID: null,
                                    seatNumber: `${
                                        parentElement?.prefix
                                            ? `${parentElement.prefix}`
                                            : ""
                                    } ${index + 1}`,
                                    userName: null,
                                })
                            );
                            updateElementProperties(selectedElementId, {
                                seats: newSeats,
                            });
                        }
                    } else {
                        updateElementProperties(selectedElementId, {
                            seats: [],
                        });
                    }
                } else if (property === "prefix") {
                    // When prefix changes, update seat names for all related seats
                    const newPrefix = e.target.value;

                    const parentElement = elements.find(
                        (element) => element.id === selectedElementId
                    );
                    if (parentElement && parentElement.seats) {
                        const updatedSeats = parentElement.seats.map(
                            (seat, index) => ({
                                ...seat,
                                seatNumber: `${newPrefix} ${index + 1}`, // Update the seat number
                            })
                        );

                        // Update the element with the new seats
                        updateElementProperties(selectedElementId, {
                            seats: updatedSeats,
                            prefix: newPrefix,
                        });
                    }
                } else {
                    const value =
                        property === "rotation" ||
                        property === "width" ||
                        property === "height" ||
                        property === "posX" ||
                        property === "posY"
                            ? parseFloat(e.target.value)
                            : e.target.value;

                    // Update the element with both the category and the entire ticket object if available
                    const updatedProperties: Partial<Element> = {
                        [property]: value,
                    };

                    // If a ticket is selected, store the ticket object in `ticket`
                    if (selectedTicket) {
                        updatedProperties.ticket_id = selectedTicket.id;
                    }

                    updateElementProperties(
                        selectedElementId,
                        updatedProperties
                    );
                }
            }
        };

        const handleDrop = (e: React.DragEvent<HTMLDivElement>) => {
            e.preventDefault();
            const elementType = e.dataTransfer.getData("text/plain");
            if (!viewerRef.current) return;
            const zoom = viewerRef.current.getZoom();
            const scrollTop = viewerRef.current.getScrollTop();
            const scrollLeft = viewerRef.current.getScrollLeft();
            const container = viewerRef.current.getContainer();
            const viewerRect = container.getBoundingClientRect();
            const posX = e.clientX - viewerRect.left - zoom;
            const posY = e.clientY - viewerRect.top - zoom;
            const newElement: Element = {
                id: elements.length + 1,
                type: elementType,
                label: elementType,
                posX,
                posY,
                width: 200,
                height: 200,
                rotation: 0,
                name: elementType,
                seats: [],
                category: eventData.tickets[0].type,
                ticket_id: eventData.tickets[0].id,
                ref: React.createRef<HTMLDivElement>(),
            };
            // Update the state with the new element
            setElements((prevElements) => {
                const newElements = [...prevElements, newElement];
                saveHistory(newElements);
                return newElements;
            });
        };
        const saveHistory = (newElements: Element[]) => {
            setHistory((prevHistory) => [...prevHistory, elements]);
            setRedoHistory([]);
        };

        const undo = () => {
            if (history.length > 0) {
                const previousState = history[history.length - 1];
                setRedoHistory((prevRedo) => [elements, ...prevRedo]);
                setHistory((prevHistory) => prevHistory.slice(0, -1));
                setElements(previousState);
            }
        };
        const redo = () => {
            if (redoHistory.length > 0) {
                const nextState = redoHistory[0];
                setHistory((prevHistory) => [...prevHistory, elements]);
                setRedoHistory((prevRedo) => prevRedo.slice(1));
                setElements(nextState);
            }
        };
        const getElementGuidelines = () => {
            return elements.map((element) => `.cube${element.id}`);
        };
        const parseTransform = (transform: string) => {
            const rotateRegex = /rotate\(([^)]+)\)/;
            const translateRegex = /translate\(([^)]+)\)/;

            const rotateMatch = transform.match(rotateRegex);
            const rotateValue = rotateMatch ? parseFloat(rotateMatch[1]) : 0;

            const translateMatch = transform.match(translateRegex);
            const translateValues = translateMatch
                ? translateMatch[1].split(",").map(parseFloat)
                : [0, 0];
            const translateX = translateValues[0] || 0;
            const translateY = translateValues[1] || 0;

            return { rotateValue, translateX, translateY };
        };
        const handleDragOver = (e: React.DragEvent<HTMLDivElement>) => {
            e.preventDefault();
        };
        const updateElementProperties = (
            id: number,
            properties: Partial<Element>
        ) => {
            setElements((elements) =>
                elements.map((el) =>
                    el.id === id ? { ...el, ...properties } : el
                )
            );
        };
        const handleSeatInputChange = (
            e: React.ChangeEvent<HTMLInputElement>,
            position: string
        ) => {
            if (selectedElementId !== null) {
                const numberOfSeats = parseInt(e.target.value, 10);
                if (!isNaN(numberOfSeats) && numberOfSeats > 0) {
                    const parentElement = elements.find(
                        (element) => element.id === selectedElementId
                    );
                    if (parentElement) {
                        const seats: Seat[] = parentElement.seats.filter(
                            (seat) => !seat.name.startsWith(`${position} Seat`)
                        );
                        const newSeats: Seat[] = Array.from(
                            { length: numberOfSeats },
                            (_, index) => ({
                                id: Date.now() + index,
                                name: `${position} Seat ${index + 1}`,
                                seatNumber: `${parentElement.prefix} ${
                                    index + 1
                                }`,
                                isClaimed: false,
                                userID: null,
                                userName: null,
                            })
                        );
                        updateElementProperties(selectedElementId, {
                            seats: [...seats, ...newSeats],
                        });
                    }
                } else {
                    const parentElement = elements.find(
                        (element) => element.id === selectedElementId
                    );
                    if (parentElement) {
                        const seats: Seat[] = parentElement.seats.filter(
                            (seat) => !seat.name.startsWith(`${position} Seat`)
                        );
                        updateElementProperties(selectedElementId, { seats });
                    }
                }
            }
        };
        const importElements = (importedElements: Element[]) => {
            const elementsWithRefs = importedElements.map((element) => ({
                ...element,
                ref: React.createRef<HTMLDivElement>(),
            }));
            setElements(elementsWithRefs);
            saveHistory(elementsWithRefs);
        };

        const handleImport = (event: React.ChangeEvent<HTMLInputElement>) => {
            const file = event.target.files?.[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const content = e.target?.result as string;
                    const importedElements = JSON.parse(content);
                    importElements(importedElements);
                };
                reader.readAsText(file);
            }
        };

        const exportElements = () => {
            const elementsWithoutRefs = elements.map(
                ({ ref, ...rest }) => rest
            );
            const dataStr =
                "data:text/json;charset=utf-8," +
                encodeURIComponent(JSON.stringify(elementsWithoutRefs));
            const downloadAnchorNode = document.createElement("a");
            downloadAnchorNode.setAttribute("href", dataStr);
            downloadAnchorNode.setAttribute("download", "elements.json");
            document.body.appendChild(downloadAnchorNode);
            downloadAnchorNode.click();
            downloadAnchorNode.remove();
        };
        const saveElements = (e) => {
            const elementsWithoutRefs = elements.map(
                ({ ref, ...rest }) => rest
            );

            const saveLayoutUrl = `/${eventData.organization.organization_slug}/${eventData.event_slug}/save-layout`;

            Inertia.post(saveLayoutUrl, {
                event_id: eventData.id,
                data: elementsWithoutRefs,
            },{preserveState:true});
            toast({
                title: "Layout saved",
                description: `Layout saved to event ${eventData.event_name}` ,
            });
        };
        const handleSelectEnd = useCallback((e: any) => {
            const moveable = moveableRef.current!;

            if (e.isDragStart) {
                e.inputEvent.preventDefault();

                moveable.waitToChangeTarget().then(() => {
                    moveable.dragStart(e.inputEvent);
                });
            }
            setTargets(e.selected as HTMLElement[]);
        }, []);
        // useEffect(() => {
        //     console.log(selectedElement);
        // });

        return (
            <>
                <div className="flex pt-5">
                    <MenuBar
                        onPasteClick={pasteElement}
                        onDeleteClick={deleteElement}
                        onDuplicateClick={duplicateElement}
                        onCenterPreviewClick={centerView}
                        onUndoClick={undo}
                        onRedoClick={redo}
                        onCopyClick={copyElement}
                        onDownloadClick={exportElements}
                        onSaveClick={saveElements}
                    />
                    <div className="flex content-center justify-center">
                        <Separator
                            orientation="vertical"
                            className="h-4 mr-2"
                        />
                        <Breadcrumb>
                            <BreadcrumbList>
                                <BreadcrumbItem className="hidden md:block">
                                    <BreadcrumbLink href="#">
                                        {organization}
                                    </BreadcrumbLink>
                                </BreadcrumbItem>
                                <BreadcrumbSeparator className="hidden md:block" />
                                <BreadcrumbItem className="hidden md:block">
                                    <BreadcrumbLink href="#">
                                        {eventData.event_name}
                                    </BreadcrumbLink>
                                </BreadcrumbItem>
                                <BreadcrumbSeparator className="hidden md:block" />
                                <BreadcrumbItem>
                                    <BreadcrumbPage>
                                        Seating Editor
                                    </BreadcrumbPage>
                                </BreadcrumbItem>
                            </BreadcrumbList>
                        </Breadcrumb>
                    </div>
                </div>
                <div className="flex w-full h-full">
                    <div className="relative flex w-full h-full">
                        <div
                            className="relative w-full h-full bg-gray-100"
                            style={{ overflow: "hidden" }}
                            onDrop={handleDrop}
                            onDragOver={handleDragOver}
                        >
                            {/* Infinite Viewer */}
                            <InfiniteViewer
                                className="w-full h-full bg-white border border-gray-300 rounded-md infinite-viewer"
                                ref={viewerRef}
                                style={{ position: "relative" }}
                                margin={0}
                                usePinch={true}
                                useWheelScroll={true}
                                useAutoZoom={true}
                            >
                                <div
                                    className="relative flex-wrap p-4 viewport selecto-area"
                                    id="container"
                                    ref={containerRef}
                                    style={{
                                        width: "100%",
                                        height: "100%",
                                        border: "2px dashed #ccc",
                                        background: "#f9f9f9",
                                        position: "absolute",
                                    }}
                                >
                                    {/* Dynamically Rendered Elements */}
                                    <ContextMenu>
                                        <ContextMenuTrigger className="w-full h-full">
                                            {elements.map((element) => (
                                                <div
                                                    key={element.id}
                                                    className={` cube cube${element.id} border`}
                                                    style={{
                                                        position: "absolute",
                                                        width: element.width,
                                                        height: element.height,
                                                        borderRadius: "8px",
                                                        transform: `rotate(${element.rotation}deg) translate(${element.posX}px, ${element.posY}px)`,
                                                        justifyContent:
                                                            "center",
                                                        color: "white",
                                                        fontWeight: "bold",
                                                        cursor: "move",
                                                    }}
                                                    data-element-id={element.id}
                                                    ref={element.ref}
                                                >
                                                    {element.type ===
                                                    "seatContainerRect" ? (
                                                        <div className="flex flex-wrap items-start justify-center w-full gap-2 p-3">
                                                            {element.seats.map(
                                                                (seat) => (
                                                                    <div
                                                                        key={
                                                                            seat.id
                                                                        }
                                                                        className="relative flex items-center justify-center w-10 h-10 transition-all ease-in-out transform bg-indigo-600 border-2 text-white border-indigo-500 rounded-full hover:scale-105"
                                                                        style={{
                                                                            fontSize:
                                                                                "12px", // Keep font size consistent
                                                                        }}
                                                                    >
                                                                        <span
                                                                            style={{
                                                                                position:
                                                                                    "absolute", // Center text
                                                                                transform:
                                                                                    "none", // Don't apply scaling to the text
                                                                            }}
                                                                        >
                                                                            {
                                                                                seat.seatNumber
                                                                            }
                                                                        </span>
                                                                    </div>
                                                                )
                                                            )}
                                                        </div>
                                                    ) : element.type ===
                                                      "seatContainerRound" ? (
                                                        <div
                                                            style={{
                                                                position:
                                                                    "absolute",
                                                                width: `${
                                                                    element.width /
                                                                    1.5
                                                                }px`,
                                                                height: `${
                                                                    element.width /
                                                                    1.5
                                                                }px`,
                                                                border: "1px solid rgba(0, 0, 0, 0.15)",
                                                                borderRadius:
                                                                    "50%",
                                                                top: "50%",
                                                                left: "50%",
                                                                transform:
                                                                    "translate(-50%, -50%)",
                                                            }}
                                                        >
                                                            {element.seats.map(
                                                                (
                                                                    seat,
                                                                    index
                                                                ) => {
                                                                    const angleStep =
                                                                        360 /
                                                                        element
                                                                            .seats
                                                                            .length;
                                                                    const angle =
                                                                        index *
                                                                        angleStep;
                                                                    return (
                                                                        <>
                                                                            <div
                                                                                key={
                                                                                    seat.id
                                                                                }
                                                                                className="absolute w-10 h-10 bg-indigo-600 border-2 border-indigo-500 rounded-full text-white flex items-center justify-center"
                                                                                style={{
                                                                                    top: "50%",
                                                                                    left: "50%",
                                                                                    transform: `translate(-50%, -50%) rotate(${angle}deg) translate(${
                                                                                        element.width /
                                                                                        2.6
                                                                                    }px) rotate(-${
                                                                                        (angle *
                                                                                            240) /
                                                                                        2
                                                                                    }deg)`,
                                                                                    transition:
                                                                                        "transform 0.3s ease, background-color 0.2s",
                                                                                }}
                                                                            >
                                                                                <span
                                                                                    style={{
                                                                                        fontSize:
                                                                                            "12px", // Keeps text centered
                                                                                    }}
                                                                                >
                                                                                    {
                                                                                        seat.seatNumber
                                                                                    }
                                                                                </span>
                                                                            </div>
                                                                        </>
                                                                    );
                                                                }
                                                            )}
                                                        </div>
                                                    ) : element.type ===
                                                      "seatContainerTable" ? (
                                                        <div
                                                            className="table border rounded"
                                                            style={{
                                                                position:
                                                                    "relative",
                                                                width: `${
                                                                    element.width -
                                                                    100
                                                                }px`, // Subtracting 100px for seat padding
                                                                height: `${
                                                                    element.height -
                                                                    100
                                                                }px`, // Same here
                                                                top: "50%",
                                                                left: "50%",
                                                                transform:
                                                                    "translate(-50%, -50%)",
                                                                display: "flex",
                                                                justifyContent:
                                                                    "center",
                                                                alignItems:
                                                                    "center",
                                                                backgroundColor:
                                                                    "#f9f9f9", // Light pastel background for table
                                                            }}
                                                        >
                                                            <div
                                                                className="top"
                                                                style={{
                                                                    position:
                                                                        "absolute", // Absolute positioning to ensure it doesn't affect parent
                                                                    top: "-40px", // Placing it outside the parent
                                                                    width: "100%",
                                                                    display:
                                                                        "flex",
                                                                    justifyContent:
                                                                        "space-around",
                                                                }}
                                                            >
                                                                {element.seats
                                                                    .filter(
                                                                        (
                                                                            seat
                                                                        ) =>
                                                                            seat.name.startsWith(
                                                                                "Top"
                                                                            )
                                                                    )
                                                                    .map(
                                                                        (
                                                                            seat
                                                                        ) => (
                                                                            <div
                                                                                key={
                                                                                    seat.id
                                                                                }
                                                                                className="w-10 h-10 chair"
                                                                                style={{
                                                                                    borderRadius:
                                                                                        "50%",
                                                                                    backgroundColor:
                                                                                        "#7c3aed", // Soft purple color
                                                                                    border: "1px solid rgba(0, 0, 0, 0.15)", // Soft border
                                                                                    margin: "4px", // Slightly more space between chairs
                                                                                }}
                                                                            >
                                                                                {
                                                                                    seat.seatNumber
                                                                                }
                                                                            </div>
                                                                        )
                                                                    )}
                                                            </div>

                                                            <div
                                                                className="left"
                                                                style={{
                                                                    position:
                                                                        "absolute",
                                                                    left: "-40px", // Placing it outside the parent
                                                                    height: "100%",
                                                                    display:
                                                                        "flex",
                                                                    flexDirection:
                                                                        "column",
                                                                    justifyContent:
                                                                        "space-around",
                                                                }}
                                                            >
                                                                {element.seats
                                                                    .filter(
                                                                        (
                                                                            seat
                                                                        ) =>
                                                                            seat.name.startsWith(
                                                                                "Left"
                                                                            )
                                                                    )
                                                                    .map(
                                                                        (
                                                                            seat
                                                                        ) => (
                                                                            <div
                                                                                key={
                                                                                    seat.id
                                                                                }
                                                                                className="w-10 h-10 chair"
                                                                                style={{
                                                                                    borderRadius:
                                                                                        "50%",
                                                                                    backgroundColor:
                                                                                        "#7c3aed", // Soft purple color
                                                                                    border: "1px solid rgba(0, 0, 0, 0.15)", // Soft border
                                                                                    margin: "4px", // Slightly more space between chairs
                                                                                }}
                                                                            >
                                                                                {
                                                                                    seat.seatNumber
                                                                                }
                                                                            </div>
                                                                        )
                                                                    )}
                                                            </div>

                                                            <div
                                                                className="right"
                                                                style={{
                                                                    position:
                                                                        "absolute",
                                                                    right: "-40px", // Placing it outside the parent
                                                                    height: "100%",
                                                                    display:
                                                                        "flex",
                                                                    flexDirection:
                                                                        "column",
                                                                    justifyContent:
                                                                        "space-around",
                                                                }}
                                                            >
                                                                {element.seats
                                                                    .filter(
                                                                        (
                                                                            seat
                                                                        ) =>
                                                                            seat.name.startsWith(
                                                                                "Right"
                                                                            )
                                                                    )
                                                                    .map(
                                                                        (
                                                                            seat
                                                                        ) => (
                                                                            <div
                                                                                key={
                                                                                    seat.id
                                                                                }
                                                                                className="w-10 h-10 chair"
                                                                                style={{
                                                                                    borderRadius:
                                                                                        "50%",
                                                                                    backgroundColor:
                                                                                        "#7c3aed", // Soft purple color
                                                                                    border: "1px solid rgba(0, 0, 0, 0.15)", // Soft border
                                                                                    margin: "4px", // Slightly more space between chairs
                                                                                }}
                                                                            >
                                                                                {
                                                                                    seat.seatNumber
                                                                                }
                                                                            </div>
                                                                        )
                                                                    )}
                                                            </div>

                                                            <div
                                                                className="bottom"
                                                                style={{
                                                                    position:
                                                                        "absolute",
                                                                    bottom: "-40px", // Placing it outside the parent
                                                                    width: "100%",
                                                                    display:
                                                                        "flex",
                                                                    justifyContent:
                                                                        "space-around",
                                                                }}
                                                            >
                                                                {element.seats
                                                                    .filter(
                                                                        (
                                                                            seat
                                                                        ) =>
                                                                            seat.name.startsWith(
                                                                                "Bottom"
                                                                            )
                                                                    )
                                                                    .map(
                                                                        (
                                                                            seat
                                                                        ) => (
                                                                            <div
                                                                                key={
                                                                                    seat.id
                                                                                }
                                                                                className="w-10 h-10 chair"
                                                                                style={{
                                                                                    borderRadius:
                                                                                        "50%",
                                                                                    backgroundColor:
                                                                                        "#7c3aed", // Soft purple color
                                                                                    border: "1px solid rgba(0, 0, 0, 0.15)", // Soft border
                                                                                    margin: "4px", // Slightly more space between chairs
                                                                                }}
                                                                            >
                                                                                {
                                                                                    seat.seatNumber
                                                                                }
                                                                            </div>
                                                                        )
                                                                    )}
                                                            </div>
                                                        </div>
                                                    ) : (
                                                        <>
                                                            <div className="flex items-center justify-center w-full h-full text-center text-black bg-slate-200">
                                                                {element.type ===
                                                                    "speakerArea" && (
                                                                    <div>
                                                                        Speaker
                                                                        Area
                                                                    </div>
                                                                )}
                                                                {element.type ===
                                                                    "foodArea" && (
                                                                    <div>
                                                                        Food
                                                                        Area
                                                                    </div>
                                                                )}
                                                                {element.type ===
                                                                    "restroom" && (
                                                                    <div>
                                                                        Restroom
                                                                    </div>
                                                                )}
                                                                {element.type ===
                                                                    "stage" && (
                                                                    <div>
                                                                        Stage
                                                                    </div>
                                                                )}
                                                                {/* Default case if type doesn't match any predefined types */}
                                                                {![
                                                                    "speakerArea",
                                                                    "foodArea",
                                                                    "restroom",
                                                                    "stage",
                                                                ].includes(
                                                                    element.type
                                                                ) && (
                                                                    <div>
                                                                        Unknown
                                                                        Area
                                                                    </div>
                                                                )}
                                                            </div>
                                                        </>
                                                    )}
                                                </div>
                                            ))}
                                        </ContextMenuTrigger>
                                        <ContextMenuContent className="w-64">
                                            <ContextMenuItem
                                                inset
                                                onClick={copyElement}
                                            >
                                                Copy
                                                <ContextMenuShortcut>
                                                    ⌘ C
                                                </ContextMenuShortcut>
                                            </ContextMenuItem>
                                            <ContextMenuItem
                                                inset
                                                onClick={pasteElement}
                                            >
                                                Paste
                                                <ContextMenuShortcut>
                                                    ⌘ P
                                                </ContextMenuShortcut>
                                            </ContextMenuItem>
                                            <ContextMenuItem
                                                inset
                                                onClick={duplicateElement}
                                            >
                                                Duplicate
                                                <ContextMenuShortcut>
                                                    ⌘ D
                                                </ContextMenuShortcut>
                                            </ContextMenuItem>
                                            <ContextMenuItem
                                                inset
                                                onClick={deleteElement}
                                            >
                                                Delete
                                                <ContextMenuShortcut>
                                                    Del
                                                </ContextMenuShortcut>
                                            </ContextMenuItem>
                                        </ContextMenuContent>
                                    </ContextMenu>

                                    {/* Moveable */}
                                    <Moveable
                                        ref={moveableRef}
                                        scrollable={true}
                                        scrollOptions={scrollOptions}
                                        target={targets}
                                        draggable={true}
                                        resizable={true}
                                        rotatable={true}
                                        throttleDrag={1}
                                        edgeDraggable={false}
                                        startDragRotate={0}
                                        throttleDragRotate={0}
                                        throttleResize={0}
                                        renderDirections={[
                                            "nw",
                                            "n",
                                            "ne",
                                            "w",
                                            "e",
                                            "sw",
                                            "s",
                                            "se",
                                        ]}
                                        throttleRotate={0}
                                        rotationPosition={"top"}
                                        snappable={true}
                                        isDisplaySnapDigit={true}
                                        isDisplayInnerSnapDigit={true}
                                        snapGap={true}
                                        keepRatio={sizeLink}
                                        snapRotationThreshold={5}
                                        snapRotationDegrees={[0, 90, 180, 270]}
                                        origin={false}
                                        onResizeStart={(e) => {
                                            e.setFixedDirection([0, 0]);
                                        }}
                                        props={{
                                            dimensionViewable: true,
                                        }}
                                        snapDirections={{
                                            top: true,
                                            left: true,
                                            bottom: true,
                                            right: true,
                                            center: true,
                                            middle: true,
                                        }}
                                        elementSnapDirections={{
                                            top: true,
                                            left: true,
                                            bottom: true,
                                            right: true,
                                            center: true,
                                            middle: true,
                                        }}
                                        elementGuidelines={getElementGuidelines()}
                                        checkInput={true}
                                        onClickGroup={(e) => {
                                            selectoRef.current!.clickTarget(
                                                e.inputEvent,
                                                e.inputTarget
                                            );
                                        }}
                                        onDrag={(e) => {
                                            e.target.style.transform =
                                                e.transform;
                                            const {
                                                translateX,
                                                translateY,
                                                rotateValue,
                                            } = parseTransform(e.transform);
                                            const targetIdMatch =
                                                e.target.className.match(
                                                    /cube(\d+)/
                                                );
                                            if (targetIdMatch) {
                                                const targetId = parseInt(
                                                    targetIdMatch[1]
                                                );
                                                updateElementProperties(
                                                    targetId,
                                                    {
                                                        posX: translateX,
                                                        posY: translateY,
                                                        rotation: rotateValue,
                                                        width: e.width,
                                                        height: e.height,
                                                    }
                                                );
                                            }
                                        }}
                                        onResize={(e) => {
                                            e.target.style.width = `${e.width}px`;
                                            e.target.style.height = `${e.height}px`;
                                            e.target.style.transform =
                                                e.drag.transform;
                                            const {
                                                translateX,
                                                translateY,
                                                rotateValue,
                                            } = parseTransform(
                                                e.drag.transform
                                            );
                                            const targetIdMatch =
                                                e.target.className.match(
                                                    /cube(\d+)/
                                                );
                                            if (targetIdMatch) {
                                                const targetId = parseInt(
                                                    targetIdMatch[1]
                                                );
                                                updateElementProperties(
                                                    targetId,
                                                    {
                                                        posX: translateX,
                                                        posY: translateY,
                                                        rotation: rotateValue,
                                                        width: e.width,
                                                        height: e.height,
                                                    }
                                                );
                                            }
                                        }}
                                        onResizeGroup={(e) => {
                                            e.events.forEach((ev) => {
                                                const targetIdMatch =
                                                    ev.target.className.match(
                                                        /cube(\d+)/
                                                    );
                                                if (targetIdMatch) {
                                                    const {
                                                        translateX,
                                                        translateY,
                                                    } = parseTransform(
                                                        ev.target.style
                                                            .transform
                                                    );
                                                    const targetId = parseInt(
                                                        targetIdMatch[1]
                                                    );
                                                    updateElementProperties(
                                                        targetId,
                                                        {
                                                            posX: translateX,
                                                            posY: translateY,
                                                        }
                                                    );
                                                }
                                            });
                                        }}
                                        onRenderGroupEnd={(e) => {
                                            e.events.forEach((ev) => {
                                                const targetIdMatch =
                                                    ev.target.className.match(
                                                        /cube(\d+)/
                                                    );
                                                if (targetIdMatch) {
                                                    const {
                                                        translateX,
                                                        translateY,
                                                        rotateValue,
                                                    } = parseTransform(
                                                        ev.target.style
                                                            .transform
                                                    );
                                                    const targetId = parseInt(
                                                        targetIdMatch[1]
                                                    );
                                                    updateElementProperties(
                                                        targetId,
                                                        {
                                                            posX: translateX,
                                                            posY: translateY,
                                                            rotation:
                                                                rotateValue,
                                                        }
                                                    );
                                                }
                                            });
                                        }}
                                        onRotate={(e) => {
                                            e.target.style.transform =
                                                e.drag.transform;
                                            const {
                                                rotateValue,
                                                translateX,
                                                translateY,
                                            } = parseTransform(
                                                e.drag.transform
                                            );
                                            const targetIdMatch =
                                                e.target.className.match(
                                                    /cube(\d+)/
                                                );
                                            if (targetIdMatch) {
                                                const targetId = parseInt(
                                                    targetIdMatch[1]
                                                );
                                                updateElementProperties(
                                                    targetId,
                                                    {
                                                        rotation: rotateValue,
                                                        posX: translateX,
                                                        posY: translateY,
                                                    }
                                                );
                                            }
                                        }}
                                        onRenderGroup={(e) => {
                                            e.events.forEach((ev) => {
                                                const cssProperties = ev.cssText
                                                    .split(";")
                                                    .filter(Boolean);
                                                const filteredProperties =
                                                    cssProperties.filter(
                                                        (property) => {
                                                            return (
                                                                !property
                                                                    .trim()
                                                                    .startsWith(
                                                                        "width:"
                                                                    ) &&
                                                                !property
                                                                    .trim()
                                                                    .startsWith(
                                                                        "height:"
                                                                    )
                                                            );
                                                        }
                                                    );
                                                const filteredCssText =
                                                    filteredProperties.join(
                                                        ";"
                                                    ) + ";";
                                                ev.target.style.cssText +=
                                                    filteredCssText;
                                            });
                                        }}
                                    />
                                </div>
                            </InfiniteViewer>
                            <Selecto
                                ref={selectoRef}
                                dragContainer={
                                    viewerRef.current?.getContainer() ||
                                    undefined
                                }
                                selectableTargets={[".selecto-area .cube"]}
                                hitRate={0}
                                selectByClick={true}
                                selectFromInside={true}
                                toggleContinueSelect={["shift"]}
                                ratio={0}
                                onDragStart={(e) => {
                                    const moveable = moveableRef.current!;
                                    const target = e.inputEvent.target;
                                    if (
                                        moveable.isMoveableElement(target) ||
                                        targets.some(
                                            (t) =>
                                                t === target ||
                                                t.contains(target)
                                        )
                                    ) {
                                        e.stop();
                                    }
                                }}
                                onSelectEnd={(e) => {
                                    const moveable = moveableRef.current!;
                                    if (e.isDragStart) {
                                        e.inputEvent.preventDefault();
                                        moveable
                                            .waitToChangeTarget()
                                            .then(() => {
                                                moveable.dragStart(
                                                    e.inputEvent
                                                );
                                            });
                                    }
                                    setTargets(e.selected);
                                    if (
                                        e.selected?.length &&
                                        e.selected.length === 1
                                    ) {
                                        setActiveSelect(true);
                                        const selectedElement = e.selected[0];
                                        const selectedId =
                                            selectedElement.getAttribute(
                                                "data-element-id"
                                            );
                                        const elementSelected = elements.find(
                                            (element) =>
                                                element.id ===
                                                Number(selectedId)
                                        );
                                        if (
                                            elementSelected?.type ===
                                            "seatContainerRound"
                                        ) {
                                            setSizeLink(true);
                                        } else {
                                            setSizeLink(false);
                                        }
                                        setSelectedElementId(
                                            Number(selectedId)
                                        );
                                    } else {
                                        setActiveSelect(false);
                                        setSelectedElementId(null);
                                        setSizeLink(false);
                                    }
                                }}
                            />
                        </div>
                    </div>
                    <div>
                        {activeSelect && selectedElement && (
                            <>
                                <div className="p-5 m-2 space-y-4 border rounded">
                                    <div>
                                        <Label htmlFor="element-name">
                                            Element
                                        </Label>
                                        <h1 className="text-slate-500">{selectedElement.name}</h1>
                                    </div>
                                    <div>
                                        <Label htmlFor="element-prefix">
                                            Prefix
                                        </Label>
                                        <Input
                                            type="text"
                                            id="element-prefix"
                                            placeholder="e.g., D - "
                                            value={selectedElement.prefix}
                                            onChange={(e) =>
                                                handleInputChange(e, "prefix")
                                            }
                                        />
                                    </div>
                                    <div>
                                        <label className="w-full max-w-xs form-control">
                                            <div className="label">
                                                <span className="label-text">
                                                    Ticket
                                                </span>
                                                <span className="label-text-alt">
                                                    String
                                                </span>
                                            </div>
                                            <Select
                                                onValueChange={(value) => {
                                                    // Parse ticket type and id from the selected value
                                                    const [type, id] =
                                                        value.split("::");

                                                    // Find the selected ticket
                                                    const selectedTicket =
                                                        eventData.tickets.find(
                                                            (ticket) =>
                                                                ticket.id ===
                                                                parseInt(id)
                                                        );

                                                    if (selectedTicket) {
                                                        // Update category (type) and ticket_id
                                                        handleInputChange(
                                                            {
                                                                target: {
                                                                    value: type,
                                                                },
                                                            } as React.ChangeEvent<HTMLInputElement>,
                                                            "category",
                                                            selectedTicket
                                                        );

                                                        handleInputChange(
                                                            {
                                                                target: {
                                                                    value: id,
                                                                },
                                                            } as React.ChangeEvent<HTMLInputElement>,
                                                            "ticket_id"
                                                        );
                                                    }
                                                }}
                                            >
                                                <SelectTrigger className="w-[280px]">
                                                    <SelectValue
                                                        placeholder={
                                                            selectedElement?.category ||
                                                            "Select a ticket type"
                                                        }
                                                    />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectGroup>
                                                        <SelectLabel>
                                                            Tickets
                                                        </SelectLabel>
                                                        {eventData.tickets.map(
                                                            (ticket) => (
                                                                <SelectItem
                                                                    key={
                                                                        ticket.id
                                                                    }
                                                                    value={`${ticket.type}::${ticket.id}`} // Combine type and id
                                                                >
                                                                    {
                                                                        ticket.type
                                                                    }{" "}
                                                                    - $
                                                                    {
                                                                        ticket.price
                                                                    }
                                                                </SelectItem>
                                                            )
                                                        )}
                                                    </SelectGroup>
                                                </SelectContent>
                                            </Select>
                                        </label>
                                    </div>

                                    <div className="grid grid-cols-2 gap-4">
                                        <div>
                                            <label className="w-full max-w-xs form-control">
                                                <Label htmlFor="width">
                                                    Width
                                                </Label>
                                                <Input
                                                    type="number"
                                                    placeholder="Type here"
                                                    value={
                                                        selectedElement.width
                                                    }
                                                    onChange={(e) =>
                                                        handleInputChange(
                                                            e,
                                                            "width"
                                                        )
                                                    }
                                                />
                                            </label>
                                        </div>
                                        <div>
                                            <Label htmlFor="height">
                                                Height
                                            </Label>
                                            <Input
                                                type="number"
                                                id="height"
                                                placeholder="Type here"
                                                value={selectedElement.height}
                                                onChange={(e) =>
                                                    handleInputChange(
                                                        e,
                                                        "height"
                                                    )
                                                }
                                            />
                                        </div>
                                    </div>
                                    <div>
                                        <Label htmlFor="rotation">
                                            Rotation
                                        </Label>
                                        <Input
                                            type="number"
                                            id="rotation"
                                            placeholder="Type here"
                                            value={selectedElement.rotation}
                                            onChange={(e) =>
                                                handleInputChange(e, "rotation")
                                            }
                                        />
                                    </div>

                                    <div className="grid grid-cols-2 gap-4">
                                        <div>
                                            <Label htmlFor="pos-x">X</Label>
                                            <Input
                                                type="number"
                                                placeholder="Type here"
                                                id="pos-x"
                                                value={selectedElement.posX}
                                                onChange={(e) =>
                                                    handleInputChange(e, "posX")
                                                }
                                            />
                                        </div>
                                        <div>
                                            <label className="w-full max-w-xs form-control">
                                                <Label htmlFor="pos-y">Y</Label>
                                                <Input
                                                    type="number"
                                                    placeholder="Type here"
                                                    value={selectedElement.posY}
                                                    onChange={(e) =>
                                                        handleInputChange(
                                                            e,
                                                            "posY"
                                                        )
                                                    }
                                                />
                                            </label>
                                        </div>
                                    </div>

                                    {(selectedElement.type ===
                                        "seatContainerRect" ||
                                        selectedElement.type ===
                                            "seatContainerRound") && (
                                        <div>
                                            <Label htmlFor="seats">Seats</Label>
                                            <Input
                                                id="seats"
                                                type="text"
                                                placeholder="Type here"
                                                value={
                                                    selectedElement.seats.length
                                                }
                                                onChange={(e) =>
                                                    handleInputChange(
                                                        e,
                                                        "seats"
                                                    )
                                                }
                                            />
                                        </div>
                                    )}

                                    {selectedElement.type ===
                                        "seatContainerTable" && (
                                        <div className="space-y-4">
                                            <div className="grid grid-cols-2 gap-4">
                                                <div>
                                                    <Label htmlFor="topSeats">
                                                        Top Seats
                                                    </Label>
                                                    <Input
                                                        id="topSeats"
                                                        type="text"
                                                        placeholder="Type here"
                                                        value={
                                                            selectedElement.seats.filter(
                                                                (seat) =>
                                                                    seat.name.startsWith(
                                                                        "Top"
                                                                    )
                                                            ).length
                                                        }
                                                        onChange={(e) =>
                                                            handleSeatInputChange(
                                                                e,
                                                                "Top"
                                                            )
                                                        }
                                                    />
                                                </div>
                                                <div>
                                                    <Label htmlFor="bottomSeats">
                                                        Bottom Seats
                                                    </Label>
                                                    <Input
                                                        id="bottomSeats"
                                                        type="text"
                                                        placeholder="Type here"
                                                        value={
                                                            selectedElement.seats.filter(
                                                                (seat) =>
                                                                    seat.name.startsWith(
                                                                        "Bottom"
                                                                    )
                                                            ).length
                                                        }
                                                        onChange={(e) =>
                                                            handleSeatInputChange(
                                                                e,
                                                                "Bottom"
                                                            )
                                                        }
                                                    />
                                                </div>
                                            </div>
                                            <div className="grid grid-cols-2 gap-4">
                                                <div>
                                                    <Label htmlFor="leftSeats">
                                                        Left Seats
                                                    </Label>
                                                    <Input
                                                        id="leftSeats"
                                                        type="text"
                                                        placeholder="Type here"
                                                        value={
                                                            selectedElement.seats.filter(
                                                                (seat) =>
                                                                    seat.name.startsWith(
                                                                        "Left"
                                                                    )
                                                            ).length
                                                        }
                                                        onChange={(e) =>
                                                            handleSeatInputChange(
                                                                e,
                                                                "Left"
                                                            )
                                                        }
                                                    />
                                                </div>
                                                <div>
                                                    <Label htmlFor="rightSeats">
                                                        Right Seats
                                                    </Label>
                                                    <Input
                                                        id="rightSeats"
                                                        type="text"
                                                        placeholder="Type here"
                                                        value={
                                                            selectedElement.seats.filter(
                                                                (seat) =>
                                                                    seat.name.startsWith(
                                                                        "Right"
                                                                    )
                                                            ).length
                                                        }
                                                        onChange={(e) =>
                                                            handleSeatInputChange(
                                                                e,
                                                                "Right"
                                                            )
                                                        }
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    )}
                                </div>
                            </>
                        )}
                    </div>
                </div>
            </>
        );
    }
);
export default Editor;

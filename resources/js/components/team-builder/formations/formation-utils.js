export const getSpecialFormation = (name, formations) => {
    if (!formations[name]) {
        console.warn(`Formación no encontrada: ${name}`);
        return [];
    }
    return formations[name].positions.map((pos, index) => ({
        ...pos,
        id: `pos-${index}`,
    }));
};

export const findFormationByName = (name, formations) => {
    return Object.keys(formations).find((formationName) =>
        name.includes(formationName)
    );
};

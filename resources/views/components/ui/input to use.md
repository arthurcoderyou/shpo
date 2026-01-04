<x-ui.input 
    id="name"
    name="name"
    type="text"
    wire:model.live="name"   
    label="Project Title"
    required  
    placeholder="Enter project title" 
    :error="$errors->first('name')"

    displayTooltip
    position="top"
    tooltipText="Please enter the official name of the project." 

    xInit="$nextTick(() => $el.focus())"
/>
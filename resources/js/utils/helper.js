export const capitalizeText = (text) => {
    if (!text) return '';
  
    return text
      .replace(/_/g, ' ')
      .split(' ')
      .filter(Boolean) 
      .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
      .join(' ');
  };
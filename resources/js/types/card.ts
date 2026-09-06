export type Card = {
    id: number;
    masked_card_number: string;
    pin: string;
    activation_date: string;
    expiration_date: string;
    balance: string;
};

export type CardFormValues = {
    card_number: string;
    pin: string;
    activation_date: string;
    expiration_date: string;
    balance: string;
};

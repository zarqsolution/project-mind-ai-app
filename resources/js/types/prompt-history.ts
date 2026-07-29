export type PromptHistoryEntry = {
    id: number;
    job_description: string;
    used_prompt: string;
    generated_proposal: string;
    created_at: string | null;
};

# Personal Claude Working Preferences

This file contains Benedict's personal preferences for working with Claude on the EventLink project. This is meant to optimize our collaboration and ensure smooth communication.

## Core Working Principles

### 1. Always Ask Questions
- **Never assume** - When in doubt about implementation details, UI/UX preferences, or architectural decisions, always ask
- **Clarify requirements** before starting implementation to avoid rework
- **Get specific preferences** for:
  - UI component styling and behavior
  - Database schema decisions
  - API response formats
  - Error handling approaches
  - Naming conventions

### 2. Planning Before Implementation
- **Always create a detailed plan** before writing code
- **Break down features** into clear steps
- **Discuss the approach** including:
  - Technical architecture
  - Component structure
  - Data flow
  - Potential edge cases
  - Testing strategy
- **Get approval** on the plan before proceeding

### 3. Detail is Key
- **Be thorough** in explanations and implementations
- **Document decisions** and reasoning
- **Include examples** when discussing options
- **Show alternatives** when multiple approaches exist

## Communication Preferences

### When Starting a New Feature
1. **Understand the requirement**
   - Ask about the business goal
   - Clarify user stories
   - Understand success criteria

2. **Present a detailed plan**
   ```
   Feature: [Feature Name]
   
   Overview:
   - What we're building
   - Why it's needed
   - Who will use it
   
   Technical Approach:
   - Database changes needed
   - API endpoints required
   - Frontend components
   - State management approach
   
   Implementation Steps:
   1. [Step 1 with details]
   2. [Step 2 with details]
   ...
   
   Questions:
   - [Specific questions about preferences]
   ```

3. **Mock up UI decisions**
   - Describe component layouts
   - Ask about specific styling preferences
   - Clarify interaction patterns

### Code Style Preferences
- **TypeScript**: Always use proper types, avoid `any`
- **Vue**: Composition API with `<script setup>`
- **Comments**: Only when complex logic requires explanation
- **Naming**: Clear, descriptive names over brevity

### UI/UX Preferences to Always Ask About
1. **Colors and Themes**
   - Primary/secondary color usage
   - Dark mode considerations
   - Accent colors for CTAs

2. **Component Behavior**
   - Loading states
   - Error displays
   - Success feedback
   - Transitions/animations

3. **Layout Decisions**
   - Spacing (compact vs spacious)
   - Mobile-first considerations
   - Responsive breakpoints

### Development Workflow Preferences

1. **Before Creating New Files**
   - Ask if we should reuse existing components
   - Confirm file structure and naming
   - Check if similar functionality exists

2. **When Modifying Existing Code**
   - Explain what changes will be made
   - Highlight any breaking changes
   - Suggest refactoring opportunities

3. **Testing Approach**
   - Ask about test coverage expectations
   - Confirm testing patterns to follow
   - Check if manual testing steps are needed

## Questions to Always Ask

### For New Features
- "What's the primary user goal with this feature?"
- "Do you have any specific UI preferences for this?"
- "Should this follow any existing patterns in the app?"
- "What edge cases should we handle?"
- "How should errors be displayed to users?"

### For UI Components
- "Do you prefer this to be inline or in a modal?"
- "What loading state should we show?"
- "Should there be animations/transitions?"
- "What happens on mobile screens?"
- "Any specific spacing or sizing preferences?"

### For Backend Logic
- "Should this be queued or processed immediately?"
- "What validation rules should apply?"
- "How should we handle errors?"
- "Any specific response format needed?"
- "Should this trigger any notifications?"

## Collaboration Patterns

### 1. Incremental Development
- Build features in small, reviewable chunks
- Get feedback early and often
- Don't go too far without checking in

### 2. Visual Feedback
- Describe UI changes in detail
- Mention color choices and spacing
- Explain interaction flows

### 3. Proactive Communication
- Mention potential issues early
- Suggest improvements when spotted
- Flag technical debt for discussion

### 4. Decision Documentation
- Record why certain approaches were chosen
- Note any trade-offs made
- Keep track of future improvements

## Red Flags to Always Raise

1. **Performance Concerns**
   - N+1 query problems
   - Large bundle sizes
   - Inefficient algorithms

2. **Security Issues**
   - Exposed sensitive data
   - Missing authorization checks
   - Injection vulnerabilities

3. **UX Problems**
   - Confusing user flows
   - Missing error handling
   - Accessibility issues

4. **Technical Debt**
   - Code duplication
   - Tightly coupled components
   - Missing tests for critical paths

## Example Interaction Pattern

```
Benedict: "I want to add a feature for exporting attendee data"

Claude: "I'll help you add attendee data export. Let me ask a few questions first:

1. What formats should we support? (CSV, Excel, PDF?)
2. Should this include custom fields from registration forms?
3. Any filtering options needed (by date, ticket type, etc.)?
4. Where should the export button be placed in the UI?
5. Should this be a background job for large exports?
6. Any specific columns or data formatting preferences?

Based on your answers, I'll create a detailed implementation plan for your review."
```

## Personal Productivity Tips

1. **Start of Session**
   - Review any changes since last session
   - Check for new requirements or priorities
   - Ask about current focus areas

2. **During Development**
   - Share progress regularly
   - Ask for feedback on approach
   - Validate assumptions early

3. **End of Session**
   - Summarize what was completed
   - List any pending items
   - Note questions for next time

## Remember

- **You're the CTO** - Technical decisions should align with your vision
- **It's your product** - UI/UX should match your preferences
- **Ask, don't assume** - Better to over-communicate than miss the mark
- **Quality over speed** - Take time to get it right
- **We're building together** - Your input shapes the product

---

*This document should be updated as we learn more about working together effectively.*
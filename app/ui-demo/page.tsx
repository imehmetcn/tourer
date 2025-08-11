'use client';

import React, { useState } from 'react';
import { 
  Button, 
  Input, 
  Select, 
  Modal, 
  Card, 
  CardHeader, 
  CardContent, 
  CardFooter,
  Loading,
  LoadingSkeleton,
  Badge 
} from '@/components/ui';

const UIDemo = () => {
  const [isModalOpen, setIsModalOpen] = useState(false);

  const selectOptions = [
    { value: 'option1', label: 'Option 1' },
    { value: 'option2', label: 'Option 2' },
    { value: 'option3', label: 'Option 3' },
  ];

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="max-w-4xl mx-auto px-4">
        <h1 className="text-3xl font-bold text-center mb-8">UI Components Demo</h1>
        
        {/* Buttons */}
        <Card className="mb-8">
          <CardHeader>
            <h2 className="text-xl font-semibold">Buttons</h2>
          </CardHeader>
          <CardContent>
            <div className="flex flex-wrap gap-4">
              <Button variant="primary">Primary Button</Button>
              <Button variant="secondary">Secondary Button</Button>
              <Button variant="outline">Outline Button</Button>
              <Button variant="ghost">Ghost Button</Button>
              <Button size="sm">Small Button</Button>
              <Button size="lg">Large Button</Button>
              <Button disabled>Disabled Button</Button>
            </div>
          </CardContent>
        </Card>

        {/* Inputs */}
        <Card className="mb-8">
          <CardHeader>
            <h2 className="text-xl font-semibold">Inputs</h2>
          </CardHeader>
          <CardContent>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <Input 
                label="Normal Input" 
                placeholder="Enter text here..."
                helperText="This is helper text"
              />
              <Input 
                label="Input with Error" 
                placeholder="Enter text here..."
                error="This field is required"
              />
              <Select 
                label="Select Dropdown"
                options={selectOptions}
                placeholder="Choose an option"
              />
              <Select 
                label="Select with Error"
                options={selectOptions}
                error="Please select an option"
              />
            </div>
          </CardContent>
        </Card>

        {/* Badges */}
        <Card className="mb-8">
          <CardHeader>
            <h2 className="text-xl font-semibold">Badges</h2>
          </CardHeader>
          <CardContent>
            <div className="flex flex-wrap gap-2">
              <Badge variant="default">Default</Badge>
              <Badge variant="primary">Primary</Badge>
              <Badge variant="secondary">Secondary</Badge>
              <Badge variant="success">Success</Badge>
              <Badge variant="warning">Warning</Badge>
              <Badge variant="error">Error</Badge>
              <Badge size="sm">Small Badge</Badge>
            </div>
          </CardContent>
        </Card>

        {/* Loading */}
        <Card className="mb-8">
          <CardHeader>
            <h2 className="text-xl font-semibold">Loading States</h2>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              <div className="flex items-center gap-4">
                <Loading size="sm" />
                <Loading size="md" />
                <Loading size="lg" />
              </div>
              <div className="space-y-2">
                <LoadingSkeleton className="h-4 w-full" />
                <LoadingSkeleton className="h-4 w-3/4" />
                <LoadingSkeleton className="h-4 w-1/2" />
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Modal */}
        <Card className="mb-8">
          <CardHeader>
            <h2 className="text-xl font-semibold">Modal</h2>
          </CardHeader>
          <CardContent>
            <Button onClick={() => setIsModalOpen(true)}>
              Open Modal
            </Button>
          </CardContent>
        </Card>

        {/* Card Examples */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <Card>
            <CardHeader>
              <h3 className="text-lg font-semibold">Card with Header</h3>
            </CardHeader>
            <CardContent>
              <p>This is the card content area. You can put any content here.</p>
            </CardContent>
            <CardFooter>
              <Button size="sm">Action</Button>
            </CardFooter>
          </Card>

          <Card>
            <CardContent>
              <h3 className="text-lg font-semibold mb-2">Simple Card</h3>
              <p>This card only has content, no header or footer.</p>
            </CardContent>
          </Card>
        </div>

        {/* Modal Component */}
        <Modal
          isOpen={isModalOpen}
          onClose={() => setIsModalOpen(false)}
          title="Example Modal"
          size="md"
        >
          <div className="space-y-4">
            <p>This is an example modal with some content.</p>
            <Input label="Name" placeholder="Enter your name" />
            <div className="flex justify-end gap-2">
              <Button variant="outline" onClick={() => setIsModalOpen(false)}>
                Cancel
              </Button>
              <Button onClick={() => setIsModalOpen(false)}>
                Save
              </Button>
            </div>
          </div>
        </Modal>
      </div>
    </div>
  );
};

export default UIDemo;